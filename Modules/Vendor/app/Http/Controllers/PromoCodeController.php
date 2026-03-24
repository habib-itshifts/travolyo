<?php

namespace Modules\Vendor\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Hotel\Models\PromoCode;
use Modules\Hotel\Services\PromoCodeService;

class PromoCodeController extends Controller
{
    public function __construct(private readonly PromoCodeService $service) {}

    public function index(Request $request): View
    {
        $promoCodes = $this->service->list($request->only(['search', 'type', 'is_active']), auth()->id());
        return view('vendor::promo-codes.index', compact('promoCodes'));
    }

    public function create(): View
    {
        return view('vendor::promo-codes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code'           => 'required|string|max:100|unique:promo_codes,code',
            'label'          => 'nullable|string|max:150',
            'type'           => 'required|in:online,offline,contracted,flash_sale',
            'description'    => 'nullable|string|max:255',
            'discount_type'  => 'nullable|in:percent,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'valid_from'     => 'nullable|date',
            'valid_until'    => 'nullable|date|after_or_equal:valid_from',
            'is_active'      => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $data['user_id'] = auth()->id();

        $this->service->store($data);
        return redirect()->route('vendor.promo-codes.index')->with('success', 'Promo code created.');
    }

    public function edit(PromoCode $promoCode): View
    {
        abort_unless($promoCode->user_id === auth()->id(), 403);
        return view('vendor::promo-codes.edit', compact('promoCode'));
    }

    public function update(Request $request, PromoCode $promoCode): RedirectResponse
    {
        abort_unless($promoCode->user_id === auth()->id(), 403);
        $data = $request->validate([
            'code'           => 'required|string|max:100|unique:promo_codes,code,' . $promoCode->id,
            'label'          => 'nullable|string|max:150',
            'type'           => 'required|in:online,offline,contracted,flash_sale',
            'description'    => 'nullable|string|max:255',
            'discount_type'  => 'nullable|in:percent,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'valid_from'     => 'nullable|date',
            'valid_until'    => 'nullable|date|after_or_equal:valid_from',
            'is_active'      => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $this->service->update($promoCode, $data);
        return redirect()->route('vendor.promo-codes.index')->with('success', 'Promo code updated.');
    }

    public function destroy(PromoCode $promoCode): RedirectResponse
    {
        abort_unless($promoCode->user_id === auth()->id(), 403);
        $this->service->delete($promoCode);
        return back()->with('success', 'Promo code deleted.');
    }
}
