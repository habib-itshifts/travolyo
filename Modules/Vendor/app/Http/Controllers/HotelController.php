<?php

namespace Modules\Vendor\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Hotel\Actions\SaveHotelAction;
use Modules\Hotel\Http\Requests\Vendor\StoreHotelRequest;
use Modules\Hotel\Http\Requests\Vendor\UpdateHotelRequest;
use Modules\Hotel\Models\Amenity;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\Service;

/**
 * Vendor HotelController
 *
 * Thin HTTP layer — all business logic lives in SaveHotelAction.
 *
 * Key differences from the Admin equivalent:
 *  - Every query is scoped to hotels owned by the authenticated vendor
 *    (author_id = auth()->id()) — a vendor cannot see or edit another vendor's hotels.
 *  - SaveHotelAction is called with isVendor: true, which enforces:
 *      status      → always "draft"  (admin must approve before going live)
 *      is_featured → always false    (admin controls homepage/featured sections)
 *      sort_order  → unchanged       (admin controls global display ordering)
 *  - The form request (Vendor\StoreHotelRequest / Vendor\UpdateHotelRequest)
 *    deliberately omits status, is_featured and sort_order fields.
 */
class HotelController extends Controller
{
    public function __construct(private readonly SaveHotelAction $saveHotel) {}

    // -------------------------------------------------------------------------
    // Index
    // -------------------------------------------------------------------------

    /**
     * List only the hotels that belong to the authenticated vendor.
     * Vendors never see each other's hotels.
     */
    public function index(Request $request): View
    {
        $hotels = Hotel::query()
            ->with('author:id,name')
            ->where('author_id', auth()->id())   // ownership scope
            ->when($request->search, fn ($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('vendor::hotels.index', compact('hotels'));
    }

    // -------------------------------------------------------------------------
    // Create
    // -------------------------------------------------------------------------

    public function create(): View
    {
        // Amenities and services are system-wide (admin-managed), shared across panels.
        $amenities = Amenity::active()->orderBy('category')->orderBy('sort_order')->get();
        $services  = Service::active()->orderBy('category')->orderBy('sort_order')->get();

        return view('vendor::hotels.create', compact('amenities', 'services'));
    }

    /**
     * Persist a new hotel under the authenticated vendor's account.
     *
     * StoreHotelRequest validates the payload (status/featured/sort_order are
     * not included in the vendor request). SaveHotelAction forces status = "draft".
     */
    public function store(StoreHotelRequest $request): RedirectResponse
    {
        $hotel = $this->saveHotel->execute(
            $request->validated(),
            isVendor: true,     // enforces draft, no featured, no sort_order change
        );

        return redirect()->route('vendor.hotels.index')
            ->with('success', 'Hotel "' . $hotel->name . '" submitted for admin review.');
    }

    // -------------------------------------------------------------------------
    // Show
    // -------------------------------------------------------------------------

    /**
     * Display a single hotel — scoped to the vendor's own hotels.
     * A vendor who guesses another hotel's ID gets a 404.
     */
    public function show(int $id): View
    {
        $hotel = Hotel::with(['amenities', 'services', 'rooms', 'deals'])
            ->where('author_id', auth()->id())
            ->findOrFail($id);

        return view('vendor::hotels.show', compact('hotel'));
    }

    // -------------------------------------------------------------------------
    // Edit / Update
    // -------------------------------------------------------------------------

    public function edit(int $id): View
    {
        $hotel = Hotel::with(['amenities', 'services'])
            ->where('author_id', auth()->id())
            ->findOrFail($id);

        $amenities = Amenity::active()->orderBy('category')->orderBy('sort_order')->get();
        $services  = Service::active()->orderBy('category')->orderBy('sort_order')->get();

        return view('vendor::hotels.edit', compact('hotel', 'amenities', 'services'));
    }

    /**
     * Update the vendor's hotel.
     *
     * Ownership is re-checked via the scoped findOrFail above (in edit).
     * We do another scope here to prevent a crafted PUT request from editing
     * a hotel that belongs to a different vendor.
     */
    public function update(UpdateHotelRequest $request, int $id): RedirectResponse
    {
        // Re-verify ownership on every write — not just on the edit form load.
        $hotel = Hotel::where('author_id', auth()->id())->findOrFail($id);

        $this->saveHotel->execute(
            $request->validated(),
            isVendor: true,   // status stays draft, featured/sort untouched
            hotel: $hotel,
        );

        return redirect()->route('vendor.hotels.index')
            ->with('success', 'Hotel "' . $hotel->name . '" updated successfully.');
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    /**
     * Soft-delete the hotel — ownership checked, admin can still restore it.
     */
    public function destroy(int $id): RedirectResponse
    {
        $hotel = Hotel::where('author_id', auth()->id())->findOrFail($id);
        $hotel->delete();

        return back()->with('success', 'Hotel "' . $hotel->name . '" deleted.');
    }
}
