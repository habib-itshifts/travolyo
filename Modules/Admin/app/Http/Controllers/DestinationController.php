<?php

namespace Modules\Admin\Http\Controllers;

use App\Models\TopDestination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DestinationController extends Controller
{
    protected function rules(): array
    {
        return [
            'city' => ['required', 'string', 'max:120'],
            'country' => ['required', 'string', 'max:120'],
            'country_code' => ['nullable', 'string', 'max:10'],
            'location' => ['nullable', 'string', 'max:20'],
            'image_path' => ['required', 'string', 'max:255'],
            'image_alt' => ['nullable', 'string', 'max:255'],
            'accommodations_label' => ['required', 'string', 'max:120'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function payload(Request $request, array $validated): array
    {
        $validated['city'] = trim((string) $validated['city']);
        $validated['country'] = trim((string) $validated['country']);
        $validated['country_code'] = strtoupper(trim((string) ($validated['country_code'] ?? '')));
        $validated['location'] = strtoupper(trim((string) ($validated['location'] ?? '')));
        $validated['image_path'] = $this->normalizeImagePath((string) $validated['image_path']);
        $validated['image_alt'] = trim((string) ($validated['image_alt'] ?? ''));
        $validated['accommodations_label'] = trim((string) $validated['accommodations_label']);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }

    protected function normalizeImagePath(string $path): string
    {
        $path = trim(str_replace('\\', '/', $path));

        if ($path === '') {
            return '';
        }

        if (Str::startsWith($path, ['http://', 'https://', '//', 'data:', 'assets/', 'uploads/'])) {
            return $path;
        }

        return 'uploads/' . ltrim($path, '/');
    }

    protected function stats(): array
    {
        $items = TopDestination::query()->get();

        return [
            'total' => $items->count(),
            'active' => $items->where('is_active', true)->count(),
            'inactive' => $items->where('is_active', false)->count(),
        ];
    }

    public function index(): View
    {
        $destinations = TopDestination::query()
            ->orderBy('sort_order')
            ->orderBy('city')
            ->get();

        return view('admin::destinations.index', [
            'destinations' => $destinations,
            'stats' => $this->stats(),
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $destination = TopDestination::query()->create($this->payload($request, $validated));

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Destination created successfully.',
                'item' => $destination->fresh(),
                'stats' => $this->stats(),
            ]);
        }

        return back()->with('success', 'Destination created successfully.');
    }

    public function update(Request $request, TopDestination $destination): JsonResponse|RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $destination->update($this->payload($request, $validated));

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Destination updated successfully.',
                'item' => $destination->fresh(),
                'stats' => $this->stats(),
            ]);
        }

        return back()->with('success', 'Destination updated successfully.');
    }

    public function destroy(Request $request, TopDestination $destination): JsonResponse|RedirectResponse
    {
        $destination->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Destination deleted successfully.',
                'stats' => $this->stats(),
            ]);
        }

        return back()->with('success', 'Destination deleted successfully.');
    }
}
