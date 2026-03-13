<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Hotel\Models\Amenity;

class AmenityController extends Controller
{
    protected function rules(?Amenity $amenity = null): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'slug' => [
                'nullable',
                'string',
                'max:120',
                Rule::unique('amenities', 'slug')->ignore($amenity?->id),
            ],
            'icon' => ['nullable', 'string', 'max:100'],
            'category' => ['required', 'string', 'max:50'],
            'applies_to' => ['required', Rule::in(['hotel', 'room', 'both'])],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function payload(Request $request, array $validated): array
    {
        $validated['slug'] = Str::slug($validated['slug'] ?: $validated['name']);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        return $validated;
    }

    protected function stats(): array
    {
        $amenities = Amenity::query()->get();

        return [
            'total' => $amenities->count(),
            'active' => $amenities->where('is_active', true)->count(),
            'hotel' => $amenities->whereIn('applies_to', ['hotel', 'both'])->count(),
            'room' => $amenities->whereIn('applies_to', ['room', 'both'])->count(),
        ];
    }

    public function index(): View
    {
        $amenities = Amenity::query()
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $stats = $this->stats();

        return view('admin::amenities.index', compact('amenities', 'stats'));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $amenity = Amenity::create($this->payload($request, $validated));

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Amenity created successfully.',
                'item' => $amenity->fresh(),
                'stats' => $this->stats(),
            ]);
        }

        return back()->with('success', 'Amenity created successfully.');
    }

    public function update(Request $request, Amenity $amenity): JsonResponse|RedirectResponse
    {
        $validated = $request->validate($this->rules($amenity));
        $amenity->update($this->payload($request, $validated));

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Amenity updated successfully.',
                'item' => $amenity->fresh(),
                'stats' => $this->stats(),
            ]);
        }

        return back()->with('success', 'Amenity updated successfully.');
    }

    public function destroy(Request $request, Amenity $amenity): JsonResponse|RedirectResponse
    {
        $amenity->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Amenity deleted successfully.',
                'stats' => $this->stats(),
            ]);
        }

        return back()->with('success', 'Amenity deleted successfully.');
    }
}
