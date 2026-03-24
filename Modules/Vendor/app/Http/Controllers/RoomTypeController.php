<?php

namespace Modules\Vendor\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
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
        return view('vendor::room-types.create');
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
            'extra_adult_price' => 'required|numeric|min:0',
            'extra_child_price' => 'required|numeric|min:0',
            'is_active'         => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $data['user_id'] = auth()->id();

        $this->service->store($data);
        return redirect()->route('vendor.room-types.index')->with('success', 'Room type created.');
    }

    public function edit(RoomType $roomType): View
    {
        abort_unless($roomType->user_id === auth()->id(), 403);
        return view('vendor::room-types.edit', compact('roomType'));
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
            'extra_adult_price' => 'required|numeric|min:0',
            'extra_child_price' => 'required|numeric|min:0',
            'is_active'         => 'boolean',
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
