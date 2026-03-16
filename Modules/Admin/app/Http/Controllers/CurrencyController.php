<?php

namespace Modules\Admin\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CurrencyController extends Controller
{
    protected function rules(?Currency $currency = null): array
    {
        return [
            'code' => [
                'required',
                'string',
                'size:3',
                Rule::unique('currencies', 'code')->ignore($currency?->id),
            ],
            'label' => ['required', 'string', 'max:10'],
            'symbol' => ['required', 'string', 'max:20'],
            'flag' => ['required', 'string', 'max:5'],
            'name' => ['required', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function payload(Request $request, array $validated): array
    {
        $validated['code'] = strtoupper($validated['code']);
        $validated['label'] = strtoupper($validated['label']);
        $validated['flag'] = strtolower($validated['flag']);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_default'] = $request->boolean('is_default');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        return $validated;
    }

    protected function normalizeDefault(Currency $currency): void
    {
        if ($currency->is_default) {
            Currency::query()
                ->where('id', '!=', $currency->id)
                ->update(['is_default' => false]);
        } elseif (!Currency::query()->where('is_default', true)->exists()) {
            $currency->forceFill(['is_default' => true])->save();
        }
    }

    protected function stats(): array
    {
        $currencies = Currency::query()->get();

        return [
            'total' => $currencies->count(),
            'active' => $currencies->where('is_active', true)->count(),
            'default' => $currencies->where('is_default', true)->count(),
            'inactive' => $currencies->where('is_active', false)->count(),
        ];
    }

    public function index(): View
    {
        $currencies = Currency::query()
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get();

        $stats = $this->stats();

        return view('admin::currencies.index', compact('currencies', 'stats'));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $currency = Currency::create($this->payload($request, $validated));
        $this->normalizeDefault($currency);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Currency created successfully.',
                'item' => $currency->fresh(),
                'stats' => $this->stats(),
            ]);
        }

        return back()->with('success', 'Currency created successfully.');
    }

    public function update(Request $request, Currency $currency): JsonResponse|RedirectResponse
    {
        $validated = $request->validate($this->rules($currency));
        $currency->update($this->payload($request, $validated));
        $this->normalizeDefault($currency);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Currency updated successfully.',
                'item' => $currency->fresh(),
                'stats' => $this->stats(),
            ]);
        }

        return back()->with('success', 'Currency updated successfully.');
    }

    public function destroy(Request $request, Currency $currency): JsonResponse|RedirectResponse
    {
        $wasDefault = $currency->is_default;
        $currency->delete();

        if ($wasDefault) {
            Currency::query()->orderBy('sort_order')->orderBy('code')->first()?->update(['is_default' => true]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Currency deleted successfully.',
                'stats' => $this->stats(),
            ]);
        }

        return back()->with('success', 'Currency deleted successfully.');
    }
}
