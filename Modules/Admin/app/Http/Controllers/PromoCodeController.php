<?php

namespace Modules\Admin\Http\Controllers;

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
        $promoCodes = $this->service->list($request);
        return view('admin::promo-codes.index', compact('promoCodes'));
    }

    public function create(): View
    {
        return view('admin::promo-codes.create');
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
            'is_active'      => 'sometimes|boolean',
        ]);

        $data['user_id']   = auth()->id();
        $data['is_active'] = $request->boolean('is_active');

        $this->service->store($data);

        return redirect()->route('admin.promo-codes.index')
            ->with('success', 'Promo code created successfully.');
    }

    public function edit(int $id): View
    {
        $promoCode = PromoCode::findOrFail($id);
        return view('admin::promo-codes.edit', compact('promoCode'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $promoCode = PromoCode::findOrFail($id);

        $data = $request->validate([
            'code'           => 'required|string|max:100|unique:promo_codes,code,' . $promoCode->id,
            'label'          => 'nullable|string|max:150',
            'type'           => 'required|in:online,offline,contracted,flash_sale',
            'description'    => 'nullable|string|max:255',
            'discount_type'  => 'nullable|in:percent,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'valid_from'     => 'nullable|date',
            'valid_until'    => 'nullable|date|after_or_equal:valid_from',
            'is_active'      => 'sometimes|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $this->service->update($promoCode, $data);

        return redirect()->route('admin.promo-codes.index')
            ->with('success', 'Promo code updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->service->delete(PromoCode::findOrFail($id));

        return back()->with('success', 'Promo code deleted.');
    }
}
