<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Hotel\Models\Service;

class ServiceController extends Controller
{
    protected function rules(?Service $service = null): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'slug' => [
                'nullable',
                'string',
                'max:120',
                Rule::unique('services', 'slug')->ignore($service?->id),
            ],
            'icon' => ['nullable', 'string', 'max:100'],
            'category' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_chargeable' => ['nullable', 'boolean'],
            'default_price' => ['nullable', 'numeric', 'min:0'],
            'price_type' => ['nullable', 'string', 'max:30'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function payload(Request $request, array $validated): array
    {
        $validated['slug'] = Str::slug($validated['slug'] ?: $validated['name']);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_chargeable'] = $request->boolean('is_chargeable');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['default_price'] = $validated['is_chargeable'] ? ($validated['default_price'] ?? null) : null;
        $validated['price_type'] = $validated['is_chargeable'] ? ($validated['price_type'] ?? null) : null;

        return $validated;
    }

    protected function stats(): array
    {
        $services = Service::query()->get();

        return [
            'total' => $services->count(),
            'active' => $services->where('is_active', true)->count(),
            'free' => $services->where('is_chargeable', false)->count(),
            'paid' => $services->where('is_chargeable', true)->count(),
        ];
    }

    public function index(): View
    {
        $services = Service::query()
            ->orderBy('category')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $stats = $this->stats();

        return view('admin::services.index', compact('services', 'stats'));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $service = Service::create($this->payload($request, $validated));

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Service created successfully.',
                'item' => $service->fresh(),
                'stats' => $this->stats(),
            ]);
        }

        return back()->with('success', 'Service created successfully.');
    }

    public function update(Request $request, Service $service): JsonResponse|RedirectResponse
    {
        $validated = $request->validate($this->rules($service));
        $service->update($this->payload($request, $validated));

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Service updated successfully.',
                'item' => $service->fresh(),
                'stats' => $this->stats(),
            ]);
        }

        return back()->with('success', 'Service updated successfully.');
    }

    public function destroy(Request $request, Service $service): JsonResponse|RedirectResponse
    {
        $service->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Service deleted successfully.',
                'stats' => $this->stats(),
            ]);
        }

        return back()->with('success', 'Service deleted successfully.');
    }
}
