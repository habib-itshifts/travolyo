<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelDeal;
use Modules\Hotel\Models\RoomType;
use Modules\Hotel\Services\HotelDealService;
use Modules\Hotel\Services\PromoCodeService;

class HotelDealController extends Controller
{
    public function __construct(
        private readonly HotelDealService $dealService,
        private readonly PromoCodeService $promoService,
    ) {}

    public function index(Request $request, Hotel $hotel): View
    {
        $deals = $this->dealService->list($hotel, $request->only(['status', 'room_type_id']));
        return view('admin::hotel-deals.index', compact('hotel', 'deals'));
    }

    public function create(Hotel $hotel): View
    {
        $roomTypes  = RoomType::active()->orderBy('name')->get();
        $promoCodes = $this->promoService->getActiveForUser();
        return view('admin::hotel-deals.create', compact('hotel', 'roomTypes', 'promoCodes'));
    }

    public function store(Request $request, Hotel $hotel): RedirectResponse
    {
        $data = $this->validateDeal($request);
        $data['hotel_id'] = $hotel->id;

        $deal = $this->dealService->store($data);
        if (!empty($data['promo_codes'])) {
            $deal->promoCodes()->sync($data['promo_codes']);
        }

        return redirect()->route('admin.hotels.deals.index', $hotel)->with('success', 'Deal created.');
    }

    public function edit(Hotel $hotel, HotelDeal $deal): View
    {
        $deal->load(['promoCodes']);
        $roomTypes  = RoomType::active()->orderBy('name')->get();
        $promoCodes = $this->promoService->getActiveForUser();
        return view('admin::hotel-deals.edit', compact('hotel', 'deal', 'roomTypes', 'promoCodes'));
    }

    public function update(Request $request, Hotel $hotel, HotelDeal $deal): RedirectResponse
    {
        $data = $this->validateDeal($request);
        $this->dealService->update($deal, $data);
        $deal->promoCodes()->sync($data['promo_codes'] ?? []);

        return redirect()->route('admin.hotels.deals.index', $hotel)->with('success', 'Deal updated.');
    }

    public function destroy(Hotel $hotel, HotelDeal $deal): RedirectResponse
    {
        $this->dealService->delete($deal);
        return back()->with('success', 'Deal deleted.');
    }

    private function validateDeal(Request $request): array
    {
        return $request->validate([
            'room_type_id'        => 'required|exists:room_types,id',
            'release_period'      => 'nullable|integer|min:0',
            'release_type'        => 'nullable|string|max:100',
            'booking_window'      => 'nullable|date',
            'cancellation_policy' => 'nullable|string|max:100',
            'blackout_dates'      => 'nullable|string',
            'special_remarks'     => 'nullable|string',
            'travel_date_start'   => 'nullable|date',
            'travel_date_end'     => 'nullable|date|after_or_equal:travel_date_start',
            'price_sgl_bb'        => 'nullable|numeric|min:0',
            'price_dbl_bb'        => 'nullable|numeric|min:0',
            'extra_bed_price'     => 'nullable|numeric|min:0',
            'child_price'         => 'nullable|numeric|min:0',
            'child_breakfast'     => 'nullable|numeric|min:0',
            'status'              => 'required|in:draft,published',
            'promo_codes'         => 'nullable|array',
            'promo_codes.*'       => 'exists:promo_codes,id',
        ]);
    }
}
