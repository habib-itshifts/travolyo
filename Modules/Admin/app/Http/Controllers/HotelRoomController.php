<?php

namespace Modules\Admin\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Hotel\Actions\SaveHotelRoomAction;
use Modules\Hotel\Http\Requests\Admin\StoreHotelRoomRequest;
use Modules\Hotel\Http\Requests\Admin\UpdateHotelRoomRequest;
use Modules\Hotel\Models\Amenity;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelRoom;

/**
 * Admin HotelRoomController
 *
 * Thin HTTP layer — all business logic lives in SaveHotelRoomAction.
 * The controller handles:
 *  - Route ↔ view wiring (index, create, edit, destroy)
 *  - Injecting StoreHotelRoomRequest / UpdateHotelRoomRequest for validation
 *  - Delegating create/update to SaveHotelRoomAction
 *
 * Admin sees all rooms for all hotels. The Vendor equivalent is scoped to
 * rooms belonging to the authenticated vendor's own hotels only.
 */
class HotelRoomController extends Controller
{
    public function __construct(private readonly SaveHotelRoomAction $saveRoom) {}

    // -------------------------------------------------------------------------
    // Index
    // -------------------------------------------------------------------------

    /**
     * List all rooms with optional hotel filter and name/type search.
     * The hotel dropdown allows the admin to drill into a single hotel's rooms.
     */
    public function index(Request $request): View
    {
        // All hotels for the filter dropdown — admin has no ownership restriction.
        $hotels = Hotel::query()->orderBy('name')->get(['id', 'name']);

        $rooms = HotelRoom::query()
            ->with(['hotel'])
            ->when($request->filled('hotel_id'), fn ($q) => $q->where('hotel_id', (int) $request->hotel_id))
            ->when($request->filled('search'), fn ($q) => $q->where(function ($inner) use ($request) {
                $inner->where('name',      'like', '%' . $request->search . '%')
                      ->orWhere('room_type', 'like', '%' . $request->search . '%')
                      ->orWhere('slug',      'like', '%' . $request->search . '%');
            }))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin::hotel-rooms.index', compact('rooms', 'hotels'));
    }

    // -------------------------------------------------------------------------
    // Create
    // -------------------------------------------------------------------------

    /**
     * Show the create-room form.
     * If hotel_id is in the query string (from "Manage Rooms" on the hotels
     * index), the hotel select is pre-selected and locked to that hotel.
     */
    public function create(Request $request): View
    {
        $hotels          = Hotel::query()->orderBy('name')->get(['id', 'name']);
        $amenities       = Amenity::forRooms()->active()->orderBy('category')->orderBy('sort_order')->get();
        $currencies      = Currency::supported();
        $selectedHotelId = $request->integer('hotel_id') ?: null;
        // A non-null $lockedHotelId renders the hotel select as disabled with a hidden input.
        $lockedHotelId   = $selectedHotelId;

        return view('admin::hotel-rooms.create', compact('hotels', 'amenities', 'currencies', 'selectedHotelId', 'lockedHotelId'));
    }

    /**
     * Persist a new room.
     *
     * StoreHotelRoomRequest handles validation.
     * is_active comes from a checkbox so it is not always present in validated()
     * — handle it explicitly via $request->boolean().
     */
    public function store(StoreHotelRoomRequest $request): RedirectResponse
    {
        $data              = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $room = $this->saveRoom->execute($data);

        return redirect()
            ->route('admin.hotel-rooms.index', ['hotel_id' => $room->hotel_id])
            ->with('success', 'Room "' . $room->name . '" created successfully.');
    }

    // -------------------------------------------------------------------------
    // Edit / Update
    // -------------------------------------------------------------------------

    public function edit(HotelRoom $hotelRoom): View
    {
        $hotelRoom->load('amenities');

        $hotels          = Hotel::query()->orderBy('name')->get(['id', 'name']);
        $amenities       = Amenity::forRooms()->active()->orderBy('category')->orderBy('sort_order')->get();
        $currencies      = Currency::supported();
        $selectedHotelId = $hotelRoom->hotel_id;
        $lockedHotelId   = null;  // on edit the admin may reassign the room to a different hotel

        return view('admin::hotel-rooms.edit', compact('hotelRoom', 'hotels', 'amenities', 'currencies', 'selectedHotelId', 'lockedHotelId'));
    }

    /**
     * Update an existing room.
     *
     * UpdateHotelRoomRequest overrides the slug rule to ignore the current room;
     * all other rules are inherited from StoreHotelRoomRequest.
     */
    public function update(UpdateHotelRoomRequest $request, HotelRoom $hotelRoom): RedirectResponse
    {
        $data              = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $this->saveRoom->execute($data, $hotelRoom);

        return redirect()
            ->route('admin.hotel-rooms.index', ['hotel_id' => $hotelRoom->hotel_id])
            ->with('success', 'Room "' . $hotelRoom->name . '" updated successfully.');
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    public function destroy(HotelRoom $hotelRoom): RedirectResponse
    {
        $hotelId = $hotelRoom->hotel_id;
        $hotelRoom->delete();

        return redirect()
            ->route('admin.hotel-rooms.index', ['hotel_id' => $hotelId])
            ->with('success', 'Room deleted successfully.');
    }
}
