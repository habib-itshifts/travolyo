<?php

namespace Modules\Vendor\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Hotel\Actions\SaveHotelRoomAction;
use Modules\Hotel\Http\Requests\Vendor\StoreHotelRoomRequest;
use Modules\Hotel\Http\Requests\Vendor\UpdateHotelRoomRequest;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelRoom;
use Modules\Hotel\Models\RoomType;

/**
 * Vendor HotelRoomController
 *
 * Thin HTTP layer — all business logic lives in SaveHotelRoomAction.
 *
 * Key differences from the Admin equivalent:
 *  - The hotel dropdown only shows hotels the vendor owns (author_id = auth()->id()).
 *  - A vendor cannot create/edit rooms for a hotel they do not own.
 *    Ownership is enforced by scoping all Hotel/HotelRoom queries through author_id.
 */
class HotelRoomController extends Controller
{
    public function __construct(private readonly SaveHotelRoomAction $saveRoom) {}

    // -------------------------------------------------------------------------
    // Index
    // -------------------------------------------------------------------------

    /**
     * List only rooms whose parent hotel belongs to the authenticated vendor.
     */
    public function index(Request $request): View
    {
        // Only the vendor's own hotels appear in the filter dropdown.
        $hotels = Hotel::query()
            ->where('author_id', auth()->id())
            ->orderBy('name')
            ->get(['id', 'name']);

        $rooms = HotelRoom::query()
            ->with(['hotel', 'roomType'])
            // Scope: only rooms for hotels the vendor owns.
            ->whereHas('hotel', fn ($q) => $q->where('author_id', auth()->id()))
            ->when($request->filled('hotel_id'), fn ($q) => $q->where('hotel_id', (int) $request->hotel_id))
            ->when($request->filled('search'), fn ($q) => $q->where('room_name', 'like', '%' . $request->search . '%'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('vendor::hotel-rooms.index', compact('rooms', 'hotels'));
    }

    // -------------------------------------------------------------------------
    // Create
    // -------------------------------------------------------------------------

    /**
     * Show the create-room form.
     *
     * The hotel select is restricted to only the vendor's own hotels.
     * If hotel_id is passed in the query string (from the "Add Room" button
     * on the hotel show page), it is pre-selected and locked.
     */
    public function create(Request $request): View
    {
        // Only own hotels — vendor cannot assign a room to another vendor's hotel.
        $hotels = Hotel::query()
            ->where('author_id', auth()->id())
            ->orderBy('name')
            ->get(['id', 'name']);

        $roomTypes       = RoomType::active()->where('user_id', auth()->id())->orderBy('sort_order')->get(['id', 'name']);
        $selectedHotelId = $request->integer('hotel_id') ?: null;

        // Reject a pre-selected hotel_id that does not belong to the vendor.
        if ($selectedHotelId && ! $hotels->contains('id', $selectedHotelId)) {
            $selectedHotelId = null;
        }

        // Non-null = hotel select rendered as disabled + hidden input.
        $lockedHotelId = $selectedHotelId;

        return view('vendor::hotel-rooms.create', compact('hotels', 'roomTypes', 'selectedHotelId', 'lockedHotelId'));
    }

    /**
     * Persist a new room.
     *
     * Before saving, we verify that the submitted hotel_id belongs to the vendor
     * to prevent privilege escalation via a crafted POST request.
     */
    public function store(StoreHotelRoomRequest $request): RedirectResponse
    {
        // 404 if the hotel does not belong to the vendor.
        Hotel::where('author_id', auth()->id())
            ->findOrFail($request->validated('hotel_id'));

        $data              = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $room = $this->saveRoom->execute($data);

        return redirect()
            ->route('vendor.hotel-rooms.index', ['hotel_id' => $room->hotel_id])
            ->with('success', 'Room "' . $room->room_name . '" created successfully.');
    }

    // -------------------------------------------------------------------------
    // Edit / Update
    // -------------------------------------------------------------------------

    /**
     * Show the edit-room form.
     *
     * The room is resolved through the vendor's own hotels — a vendor who
     * guesses another hotel's room ID will receive a 404.
     */
    public function edit(int $hotelRoom): View
    {
        $room = HotelRoom::whereHas('hotel', fn ($q) => $q->where('author_id', auth()->id()))
            ->findOrFail($hotelRoom);

        $hotels = Hotel::query()
            ->where('author_id', auth()->id())
            ->orderBy('name')
            ->get(['id', 'name']);

        $roomTypes       = RoomType::active()->where('user_id', auth()->id())->orderBy('sort_order')->get(['id', 'name']);
        $selectedHotelId = $room->hotel_id;
        $lockedHotelId   = null;  // vendor may reassign room to one of their own hotels

        return view('vendor::hotel-rooms.edit', compact('room', 'hotels', 'roomTypes', 'selectedHotelId', 'lockedHotelId'));
    }

    /**
     * Update the room — ownership is re-verified on every write operation.
     */
    public function update(UpdateHotelRoomRequest $request, int $hotelRoom): RedirectResponse
    {
        // Re-verify ownership of the room.
        $room = HotelRoom::whereHas('hotel', fn ($q) => $q->where('author_id', auth()->id()))
            ->findOrFail($hotelRoom);

        // If the hotel_id changed, ensure the new hotel also belongs to the vendor.
        Hotel::where('author_id', auth()->id())
            ->findOrFail($request->validated('hotel_id'));

        $data              = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $this->saveRoom->execute($data, $room);

        return redirect()
            ->route('vendor.hotel-rooms.index', ['hotel_id' => $room->hotel_id])
            ->with('success', 'Room "' . $room->room_name . '" updated successfully.');
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    public function destroy(int $hotelRoom): RedirectResponse
    {
        $room = HotelRoom::whereHas('hotel', fn ($q) => $q->where('author_id', auth()->id()))
            ->findOrFail($hotelRoom);

        $hotelId = $room->hotel_id;
        $room->delete();

        return redirect()
            ->route('vendor.hotel-rooms.index', ['hotel_id' => $hotelId])
            ->with('success', 'Room deleted successfully.');
    }
}
