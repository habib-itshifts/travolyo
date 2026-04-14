<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Hotel\Models\Amenity;
use Modules\Space\Actions\SaveSpaceAction;
use Modules\Space\Http\Requests\Admin\StoreSpaceRequest;
use Modules\Space\Http\Requests\Admin\UpdateSpaceRequest;
use Modules\Space\Models\Space;
use Modules\Space\Models\SpaceAvailability;

class SpaceController extends Controller
{
    public function __construct(private readonly SaveSpaceAction $saveSpace) {}

    // -------------------------------------------------------------------------
    // Index
    // -------------------------------------------------------------------------

    public function index(Request $request): View
    {
        $spaces = Space::withTrashed()
            ->with('author:id,name')
            ->when($request->search, fn ($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->city, fn ($q) => $q->where('city', 'like', '%' . $request->city . '%'))
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin::spaces.index', compact('spaces'));
    }

    // -------------------------------------------------------------------------
    // Create
    // -------------------------------------------------------------------------

    public function create(): View
    {
        $amenities = Amenity::active()->orderBy('category')->orderBy('sort_order')->get();

        return view('admin::spaces.create', compact('amenities'));
    }

    public function store(StoreSpaceRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_featured'] = $request->boolean('is_featured');

        $space = $this->saveSpace->execute($data, isVendor: false);

        return redirect()->route('admin.spaces.index')
            ->with('success', 'Space "' . $space->name . '" created successfully.');
    }

    // -------------------------------------------------------------------------
    // Show
    // -------------------------------------------------------------------------

    public function show(int $id): View
    {
        $space = Space::with(['amenities', 'availabilities', 'spaceBookings'])->findOrFail($id);

        return view('admin::spaces.show', compact('space'));
    }

    // -------------------------------------------------------------------------
    // Edit / Update
    // -------------------------------------------------------------------------

    public function edit(int $id): View
    {
        $space     = Space::with('amenities')->findOrFail($id);
        $amenities = Amenity::active()->orderBy('category')->orderBy('sort_order')->get();

        return view('admin::spaces.edit', compact('space', 'amenities'));
    }

    public function update(UpdateSpaceRequest $request, int $id): RedirectResponse
    {
        $space = Space::findOrFail($id);
        $data  = $request->validated();
        $data['is_featured'] = $request->boolean('is_featured');

        $this->saveSpace->execute($data, isVendor: false, space: $space);

        return redirect()->route('admin.spaces.index')
            ->with('success', 'Space "' . $space->name . '" updated successfully.');
    }

    // -------------------------------------------------------------------------
    // Delete / Restore
    // -------------------------------------------------------------------------

    public function destroy(int $id): RedirectResponse
    {
        $space = Space::findOrFail($id);
        $space->delete();

        return back()->with('success', 'Space "' . $space->name . '" deleted.');
    }

    public function restore(int $id): RedirectResponse
    {
        Space::withTrashed()->findOrFail($id)->restore();

        return back()->with('success', 'Space restored successfully.');
    }

    // -------------------------------------------------------------------------
    // Availability Management
    // -------------------------------------------------------------------------

    public function availability(int $id): View
    {
        $space = Space::findOrFail($id);
        $availabilities = $space->availabilities()
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->get();

        return view('admin::spaces.availability', compact('space', 'availabilities'));
    }

    public function updateAvailability(Request $request, int $id): RedirectResponse
    {
        $space = Space::findOrFail($id);

        $request->validate([
            'dates'          => ['required', 'array'],
            'dates.*'        => ['date_format:Y-m-d'],
            'is_available'   => ['required', 'boolean'],
            'price_override' => ['nullable', 'numeric', 'min:0'],
            'notes'          => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($request->dates as $date) {
            SpaceAvailability::updateOrCreate(
                ['space_id' => $space->id, 'date' => $date],
                [
                    'is_available'   => $request->boolean('is_available'),
                    'price_override' => $request->price_override,
                    'notes'          => $request->notes,
                ],
            );
        }

        return back()->with('success', 'Availability updated for ' . count($request->dates) . ' date(s).');
    }
}
