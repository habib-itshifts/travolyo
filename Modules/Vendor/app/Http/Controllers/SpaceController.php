<?php

namespace Modules\Vendor\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Hotel\Models\Amenity;
use Modules\Space\Actions\SaveSpaceAction;
use Modules\Space\Http\Requests\Vendor\StoreSpaceRequest;
use Modules\Space\Http\Requests\Vendor\UpdateSpaceRequest;
use Modules\Space\Models\Space;
use Modules\Space\Models\SpaceAvailability;

/**
 * Vendor SpaceController
 *
 * Every query is scoped to spaces owned by the authenticated vendor.
 * SaveSpaceAction is called with isVendor: true to enforce draft status.
 */
class SpaceController extends Controller
{
    public function __construct(private readonly SaveSpaceAction $saveSpace) {}

    // -------------------------------------------------------------------------
    // Index
    // -------------------------------------------------------------------------

    public function index(Request $request): View
    {
        $spaces = Space::query()
            ->with('author:id,name')
            ->where('author_id', auth()->id())
            ->when($request->search, fn ($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('vendor::spaces.index', compact('spaces'));
    }

    // -------------------------------------------------------------------------
    // Create
    // -------------------------------------------------------------------------

    public function create(): View
    {
        $amenities = Amenity::active()->orderBy('category')->orderBy('sort_order')->get();

        return view('vendor::spaces.create', compact('amenities'));
    }

    public function store(StoreSpaceRequest $request): RedirectResponse
    {
        $space = $this->saveSpace->execute(
            $request->validated(),
            isVendor: true,
        );

        return redirect()->route('vendor.spaces.index')
            ->with('success', 'Space "' . $space->name . '" submitted for admin review.');
    }

    // -------------------------------------------------------------------------
    // Show
    // -------------------------------------------------------------------------

    public function show(int $id): View
    {
        $space = Space::with(['amenities', 'availabilities', 'spaceBookings'])
            ->where('author_id', auth()->id())
            ->findOrFail($id);

        return view('vendor::spaces.show', compact('space'));
    }

    // -------------------------------------------------------------------------
    // Edit / Update
    // -------------------------------------------------------------------------

    public function edit(int $id): View
    {
        $space = Space::with('amenities')
            ->where('author_id', auth()->id())
            ->findOrFail($id);

        $amenities = Amenity::active()->orderBy('category')->orderBy('sort_order')->get();

        return view('vendor::spaces.edit', compact('space', 'amenities'));
    }

    public function update(UpdateSpaceRequest $request, int $id): RedirectResponse
    {
        $space = Space::where('author_id', auth()->id())->findOrFail($id);

        $this->saveSpace->execute(
            $request->validated(),
            isVendor: true,
            space: $space,
        );

        return redirect()->route('vendor.spaces.index')
            ->with('success', 'Space "' . $space->name . '" updated successfully.');
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    public function destroy(int $id): RedirectResponse
    {
        $space = Space::where('author_id', auth()->id())->findOrFail($id);
        $space->delete();

        return back()->with('success', 'Space "' . $space->name . '" deleted.');
    }

    // -------------------------------------------------------------------------
    // Availability Management
    // -------------------------------------------------------------------------

    public function availability(int $id): View
    {
        $space = Space::where('author_id', auth()->id())->findOrFail($id);
        $availabilities = $space->availabilities()
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->get();

        return view('vendor::spaces.availability', compact('space', 'availabilities'));
    }

    public function updateAvailability(Request $request, int $id): RedirectResponse
    {
        $space = Space::where('author_id', auth()->id())->findOrFail($id);

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
