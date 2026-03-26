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
            'name'          => ['required', 'string', 'max:100'],
            'symbol'        => ['required', 'string', 'max:25'],
            'format'        => ['required', 'string', 'max:50'],
            'exchange_rate' => ['required', 'numeric', 'min:0'],
            'active'        => ['nullable', 'boolean'],
        ];
    }

    protected function payload(Request $request, array $validated): array
    {
        $validated['code']   = strtoupper($validated['code']);
        $validated['active'] = $request->boolean('active');

        return $validated;
    }

    protected function stats(): array
    {
        $currencies = Currency::query()->get();

        return [
            'total'    => $currencies->count(),
            'active'   => $currencies->where('active', true)->count(),
            'inactive' => $currencies->where('active', false)->count(),
        ];
    }

    public function index(): View
    {
        $currencies = Currency::query()
            ->orderBy('code')
            ->get();

        $stats = $this->stats();

        return view('admin::currencies.index', compact('currencies', 'stats'));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $currency = Currency::create($this->payload($request, $validated));

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Currency created successfully.',
                'item'    => $currency->fresh(),
                'stats'   => $this->stats(),
            ]);
        }

        return back()->with('success', 'Currency created successfully.');
    }

    public function update(Request $request, Currency $currency): JsonResponse|RedirectResponse
    {
        $validated = $request->validate($this->rules($currency));
        $currency->update($this->payload($request, $validated));

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Currency updated successfully.',
                'item'    => $currency->fresh(),
                'stats'   => $this->stats(),
            ]);
        }

        return back()->with('success', 'Currency updated successfully.');
    }

    public function destroy(Request $request, Currency $currency): JsonResponse|RedirectResponse
    {
        $currency->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Currency deleted successfully.',
                'stats'   => $this->stats(),
            ]);
        }

        return back()->with('success', 'Currency deleted successfully.');
    }
}
