<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Hotel\Actions\SaveHotelAction;
use Modules\Hotel\Http\Requests\Admin\StoreHotelRequest;
use Modules\Hotel\Http\Requests\Admin\UpdateHotelRequest;
use Modules\Hotel\Models\Amenity;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\Service;

/**
 * Admin HotelController
 *
 * Thin HTTP layer — all business logic lives in SaveHotelAction.
 * The controller handles:
 *  - Route ↔ view wiring (index, create, show, edit, destroy, restore)
 *  - Injecting the correct FormRequest (StoreHotelRequest / UpdateHotelRequest)
 *  - Passing isVendor: false to SaveHotelAction so admins get full status control
 *
 * The controller does NOT contain any payload-building, media-resolution,
 * or repeater-normalisation logic — those responsibilities belong to
 * SaveHotelAction and the FormRequest classes.
 */
class HotelController extends Controller
{
    public function __construct(private readonly SaveHotelAction $saveHotel) {}

    // -------------------------------------------------------------------------
    // Index
    // -------------------------------------------------------------------------

    /**
     * List all hotels (including soft-deleted rows) with optional filters.
     * Admin sees every hotel in the system regardless of owner.
     */
    public function index(Request $request): View
    {
        $hotels = Hotel::withTrashed()
            ->with('author:id,name')
            ->when($request->search, fn ($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->city,   fn ($q) => $q->where('city',   'like', '%' . $request->city . '%'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin::hotels.index', compact('hotels'));
    }

    // -------------------------------------------------------------------------
    // Create
    // -------------------------------------------------------------------------

    public function create(): View
    {
        $amenities = Amenity::active()->orderBy('category')->orderBy('sort_order')->get();
        $services  = Service::active()->orderBy('category')->orderBy('sort_order')->get();

        return view('admin::hotels.create', compact('amenities', 'services'));
    }

    /**
     * Persist a new hotel.
     *
     * StoreHotelRequest validates the payload (including the admin-only status,
     * is_featured, sort_order fields). SaveHotelAction handles the rest.
     * isVendor: false — admin may set any status on creation.
     */
    public function store(StoreHotelRequest $request): RedirectResponse
    {
        $data             = $request->validated();
        // Checkbox: not submitted when unchecked, handle here before action.
        $data['is_featured'] = $request->boolean('is_featured');

        $hotel = $this->saveHotel->execute($data, isVendor: false);

        return redirect()->route('admin.hotels.index')
            ->with('success', 'Hotel "' . $hotel->name . '" created successfully.');
    }

    // -------------------------------------------------------------------------
    // Show
    // -------------------------------------------------------------------------

    public function show(int $id): View
    {
        $hotel = Hotel::with(['amenities', 'services', 'rooms', 'deals'])->findOrFail($id);

        return view('admin::hotels.show', compact('hotel'));
    }

    // -------------------------------------------------------------------------
    // Edit / Update
    // -------------------------------------------------------------------------

    public function edit(int $id): View
    {
        $hotel     = Hotel::with(['amenities', 'services'])->findOrFail($id);
        $amenities = Amenity::active()->orderBy('category')->orderBy('sort_order')->get();
        $services  = Service::active()->orderBy('category')->orderBy('sort_order')->get();

        return view('admin::hotels.edit', compact('hotel', 'amenities', 'services'));
    }

    /**
     * Update an existing hotel.
     *
     * UpdateHotelRequest overrides only the slug uniqueness rule (ignores the
     * current hotel's slug). All other admin rules come from StoreHotelRequest
     * via inheritance.
     */
    public function update(UpdateHotelRequest $request, int $id): RedirectResponse
    {
        $hotel            = Hotel::findOrFail($id);
        $data             = $request->validated();
        $data['is_featured'] = $request->boolean('is_featured');

        $this->saveHotel->execute($data, isVendor: false, hotel: $hotel);

        return redirect()->route('admin.hotels.index')
            ->with('success', 'Hotel "' . $hotel->name . '" updated successfully.');
    }

    // -------------------------------------------------------------------------
    // Delete / Restore
    // -------------------------------------------------------------------------

    public function destroy(int $id): RedirectResponse
    {
        $hotel = Hotel::findOrFail($id);
        $hotel->delete();

        return back()->with('success', 'Hotel "' . $hotel->name . '" deleted.');
    }

    public function restore(int $id): RedirectResponse
    {
        Hotel::withTrashed()->findOrFail($id)->restore();

        return back()->with('success', 'Hotel restored successfully.');
    }
}
