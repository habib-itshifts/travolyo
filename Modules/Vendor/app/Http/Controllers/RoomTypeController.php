<?php

namespace Modules\Vendor\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Hotel\Models\Amenity;
use Modules\Hotel\Models\RoomType;
use Modules\Hotel\Services\RoomTypeService;

class RoomTypeController extends Controller
{
    public function __construct(private readonly RoomTypeService $service) {}

    public function index(Request $request): View
    {
        $roomTypes = $this->service->list($request->only(['search', 'is_active']), auth()->id());
        return view('vendor::room-types.index', compact('roomTypes'));
    }

    public function create(): View
    {
        $amenities = Amenity::forRooms()->active()->orderBy('category')->orderBy('sort_order')->get();
        return view('vendor::room-types.create', compact('amenities'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'              => 'required|string|max:191',
            'image_id'          => 'nullable|integer',
            'bed_configuration' => 'nullable|array',
            'max_adults'        => 'required|integer|min:1|max:20',
            'max_children'      => 'required|integer|min:0|max:10',
            'max_occupancy'     => 'required|integer|min:1|max:30',
            'size_sqm'          => 'nullable|numeric|min:0',
            'view_type'         => 'nullable|string|max:50',
            'description'       => 'nullable|string',
            'price_sgl_bb'      => 'nullable|numeric|min:0',
            'price_dbl_bb'      => 'nullable|numeric|min:0',
            'extra_bed_price'   => 'nullable|numeric|min:0',
            'child_price'       => 'nullable|numeric|min:0',
            'child_breakfast'   => 'nullable|numeric|min:0',
            'extra_adult_price' => 'required|numeric|min:0',
            'extra_child_price' => 'required|numeric|min:0',
            'is_active'         => 'boolean',
            'amenity_ids'       => 'nullable|array',
            'amenity_ids.*'     => 'integer|exists:amenities,id',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $data['user_id'] = auth()->id();

        $this->service->store($data);
        return redirect()->route('vendor.room-types.index')->with('success', 'Room type created.');
    }

    public function edit(RoomType $roomType): View
    {
        abort_unless($roomType->user_id === auth()->id(), 403);
        $roomType->load('amenities');
        $amenities = Amenity::forRooms()->active()->orderBy('category')->orderBy('sort_order')->get();
        return view('vendor::room-types.edit', compact('roomType', 'amenities'));
    }

    public function update(Request $request, RoomType $roomType): RedirectResponse
    {
        abort_unless($roomType->user_id === auth()->id(), 403);
        $data = $request->validate([
            'name'              => 'required|string|max:191',
            'image_id'          => 'nullable|integer',
            'bed_configuration' => 'nullable|array',
            'max_adults'        => 'required|integer|min:1|max:20',
            'max_children'      => 'required|integer|min:0|max:10',
            'max_occupancy'     => 'required|integer|min:1|max:30',
            'size_sqm'          => 'nullable|numeric|min:0',
            'view_type'         => 'nullable|string|max:50',
            'description'       => 'nullable|string',
            'price_sgl_bb'      => 'nullable|numeric|min:0',
            'price_dbl_bb'      => 'nullable|numeric|min:0',
            'extra_bed_price'   => 'nullable|numeric|min:0',
            'child_price'       => 'nullable|numeric|min:0',
            'child_breakfast'   => 'nullable|numeric|min:0',
            'extra_adult_price' => 'required|numeric|min:0',
            'extra_child_price' => 'required|numeric|min:0',
            'is_active'         => 'boolean',
            'amenity_ids'       => 'nullable|array',
            'amenity_ids.*'     => 'integer|exists:amenities,id',
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $this->service->update($roomType, $data);
        return redirect()->route('vendor.room-types.index')->with('success', 'Room type updated.');
    }

    public function destroy(RoomType $roomType): RedirectResponse
    {
        abort_unless($roomType->user_id === auth()->id(), 403);
        $this->service->delete($roomType);
        return back()->with('success', 'Room type deleted.');
    }
}
