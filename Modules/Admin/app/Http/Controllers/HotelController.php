<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\Hotel\Models\Amenity;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\Service;

class HotelController extends Controller
{
    public function index(Request $request): View
    {
        $hotels = Hotel::withTrashed()
            ->when($request->search, fn ($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->city, fn ($q) => $q->where('city', 'like', '%' . $request->city . '%'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin::hotels.index', compact('hotels'));
    }

    public function create(): View
    {
        $amenities = Amenity::active()->orderBy('category')->orderBy('sort_order')->get();
        $services  = Service::active()->orderBy('category')->orderBy('sort_order')->get();

        return view('admin::hotels.create', compact('amenities', 'services'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:191'],
            'slug'              => ['nullable', 'string', 'max:191', 'unique:hotels,slug'],
            'star_rating'       => ['nullable', 'integer', 'between:1,5'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description'       => ['nullable', 'string'],
            'address'           => ['required', 'string', 'max:255'],
            'city'              => ['required', 'string', 'max:100'],
            'state'             => ['nullable', 'string', 'max:100'],
            'country'           => ['required', 'string', 'size:2'],
            'postal_code'       => ['nullable', 'string', 'max:20'],
            'latitude'          => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'         => ['nullable', 'numeric', 'between:-180,180'],
            'email'             => ['nullable', 'email', 'max:191'],
            'phone'             => ['nullable', 'string', 'max:50'],
            'website'           => ['nullable', 'url', 'max:255'],
            'check_in_time'     => ['nullable', 'string', 'max:5'],
            'check_out_time'    => ['nullable', 'string', 'max:5'],
            'payment_methods'   => ['nullable', 'array'],
            'languages_spoken'  => ['nullable', 'array'],
            'status'            => ['required', 'in:draft,active,inactive,suspended'],
            'is_featured'       => ['nullable', 'boolean'],
            'sort_order'        => ['nullable', 'integer', 'min:0'],
            'amenity_ids'       => ['nullable', 'array'],
            'amenity_ids.*'     => ['integer', 'exists:amenities,id'],
            'service_ids'       => ['nullable', 'array'],
            'service_ids.*'     => ['integer', 'exists:services,id'],
        ]);

        $validated['slug']       = $validated['slug'] ?: Str::slug($validated['name']);
        $validated['user_id']    = auth()->id();
        $validated['is_featured']= $request->boolean('is_featured');

        $hotel = Hotel::create($validated);

        if (! empty($validated['amenity_ids'])) {
            $hotel->amenities()->sync($validated['amenity_ids']);
        }

        if (! empty($validated['service_ids'])) {
            $hotel->services()->sync($validated['service_ids']);
        }

        return redirect()->route('admin.hotels.index')
            ->with('success', 'Hotel "' . $hotel->name . '" created successfully.');
    }

    public function show(int $id): View
    {
        $hotel = Hotel::with(['amenities', 'services', 'rooms', 'deals'])->findOrFail($id);

        return view('admin::hotels.show', compact('hotel'));
    }

    public function edit(int $id): View
    {
        $hotel     = Hotel::with(['amenities', 'services'])->findOrFail($id);
        $amenities = Amenity::active()->orderBy('category')->orderBy('sort_order')->get();
        $services  = Service::active()->orderBy('category')->orderBy('sort_order')->get();

        return view('admin::hotels.edit', compact('hotel', 'amenities', 'services'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $hotel = Hotel::findOrFail($id);

        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:191'],
            'slug'              => ['nullable', 'string', 'max:191', 'unique:hotels,slug,' . $id],
            'star_rating'       => ['nullable', 'integer', 'between:1,5'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description'       => ['nullable', 'string'],
            'address'           => ['required', 'string', 'max:255'],
            'city'              => ['required', 'string', 'max:100'],
            'state'             => ['nullable', 'string', 'max:100'],
            'country'           => ['required', 'string', 'size:2'],
            'postal_code'       => ['nullable', 'string', 'max:20'],
            'latitude'          => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'         => ['nullable', 'numeric', 'between:-180,180'],
            'email'             => ['nullable', 'email', 'max:191'],
            'phone'             => ['nullable', 'string', 'max:50'],
            'website'           => ['nullable', 'url', 'max:255'],
            'check_in_time'     => ['nullable', 'string', 'max:5'],
            'check_out_time'    => ['nullable', 'string', 'max:5'],
            'payment_methods'   => ['nullable', 'array'],
            'languages_spoken'  => ['nullable', 'array'],
            'status'            => ['required', 'in:draft,active,inactive,suspended'],
            'is_featured'       => ['nullable', 'boolean'],
            'sort_order'        => ['nullable', 'integer', 'min:0'],
            'amenity_ids'       => ['nullable', 'array'],
            'amenity_ids.*'     => ['integer', 'exists:amenities,id'],
            'service_ids'       => ['nullable', 'array'],
            'service_ids.*'     => ['integer', 'exists:services,id'],
        ]);

        $validated['slug']        = $validated['slug'] ?: Str::slug($validated['name']);
        $validated['is_featured'] = $request->boolean('is_featured');

        $hotel->update($validated);
        $hotel->amenities()->sync($validated['amenity_ids'] ?? []);
        $hotel->services()->sync($validated['service_ids'] ?? []);

        return redirect()->route('admin.hotels.index')
            ->with('success', 'Hotel "' . $hotel->name . '" updated successfully.');
    }

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
