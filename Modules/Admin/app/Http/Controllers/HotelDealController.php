<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelDeal;
use Modules\Hotel\Models\PromoCode;
use Modules\Hotel\Services\HotelDealService;

class HotelDealController extends Controller
{
    public function __construct(private readonly HotelDealService $service) {}

    public function index(Request $request, int $hotel): View
    {
        $hotel = Hotel::findOrFail($hotel);
        $deals = $this->service->list($hotel, $request);

        return view('admin::hotel-deals.index', compact('hotel', 'deals'));
    }

    public function create(int $hotel): View
    {
        $hotel      = Hotel::with('rooms')->findOrFail($hotel);
        $promoCodes = PromoCode::active()->orderBy('code')->get();

        return view('admin::hotel-deals.create', compact('hotel', 'promoCodes'));
    }

    public function store(Request $request, int $hotel): RedirectResponse
    {
        $hotel = Hotel::findOrFail($hotel);

        $data = $this->validateDeal($request);
        $this->service->store($hotel, $data, isVendor: false);

        return redirect()->route('admin.hotels.deals.index', $hotel->id)
            ->with('success', 'Deal created successfully.');
    }

    public function edit(int $hotel, int $deal): View
    {
        $hotel      = Hotel::with('rooms')->findOrFail($hotel);
        $deal       = HotelDeal::with(['rates', 'promoCodes'])->where('hotel_id', $hotel->id)->findOrFail($deal);
        $promoCodes = PromoCode::active()->orderBy('code')->get();

        return view('admin::hotel-deals.edit', compact('hotel', 'deal', 'promoCodes'));
    }

    public function update(Request $request, int $hotel, int $deal): RedirectResponse
    {
        $hotel = Hotel::findOrFail($hotel);
        $deal  = HotelDeal::where('hotel_id', $hotel->id)->findOrFail($deal);

        $data = $this->validateDeal($request, $deal);
        $this->service->update($deal, $data, isVendor: false);

        return redirect()->route('admin.hotels.deals.index', $hotel->id)
            ->with('success', 'Deal updated successfully.');
    }

    public function destroy(int $hotel, int $deal): RedirectResponse
    {
        $deal = HotelDeal::where('hotel_id', $hotel)->findOrFail($deal);
        $this->service->delete($deal);

        return back()->with('success', 'Deal deleted.');
    }

    private function validateDeal(Request $request, ?HotelDeal $deal = null): array
    {
        return $request->validate([
            'hotel_room_id'       => 'nullable|exists:hotel_rooms,id',
            'room_type'           => 'required|string|max:100',
            'release_period'      => 'nullable|string|max:100',
            'booking_window'      => 'nullable|string|max:100',
            'cancellation_policy' => 'nullable|string|max:100',
            'max_occupancy_label' => 'nullable|string|max:150',
            'allocation'          => 'nullable|string|max:100',
            'blackout_dates'      => 'nullable|array',
            'blackout_dates.*'    => 'date',
            'special_remarks'     => 'nullable|string',
            'status'              => 'required|in:draft,published',
            'promo_codes'         => 'nullable|array',
            'promo_codes.*'       => 'exists:promo_codes,id',
            'rates'               => 'nullable|array',
            'rates.*.travel_date_start' => 'required|date',
            'rates.*.travel_date_end'   => 'required|date|after_or_equal:rates.*.travel_date_start',
            'rates.*.price_sgl_bb'      => 'nullable|numeric|min:0',
            'rates.*.price_dbl_bb'      => 'nullable|numeric|min:0',
            'rates.*.extra_bed_price'   => 'nullable|numeric|min:0',
            'rates.*.child_price'       => 'nullable|numeric|min:0',
            'rates.*.child_breakfast'   => 'nullable|numeric|min:0',
        ]);
    }
}
