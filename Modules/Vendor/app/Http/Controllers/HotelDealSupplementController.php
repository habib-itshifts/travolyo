<?php

namespace Modules\Vendor\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Hotel\Models\HotelDeal;
use Modules\Hotel\Models\HotelDealSupplement;
use Modules\Hotel\Services\HotelDealSupplementService;

class HotelDealSupplementController extends Controller
{
    public function __construct(private readonly HotelDealSupplementService $service) {}

    private function vendorDeals()
    {
        return HotelDeal::whereHas('hotel', fn ($q) => $q->where('author_id', auth()->id()))
            ->with('hotel:id,name', 'roomType:id,name')
            ->orderByDesc('id')
            ->get();
    }

    public function index(Request $request): View
    {
        $supplements = $this->service->list($request->only(['search', 'deal_id']), auth()->id());
        $deals = $this->vendorDeals();
        return view('vendor::hotel-deal-supplements.index', compact('supplements', 'deals'));
    }

    public function create(): View
    {
        $deals = $this->vendorDeals();
        return view('vendor::hotel-deal-supplements.create', compact('deals'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'hotel_deal_id' => 'required|exists:hotel_deals,id',
            'event_name'    => 'required|string|max:100',
            'date_start'    => 'required|date',
            'date_end'      => 'required|date|after_or_equal:date_start',
            'amount'        => 'required|numeric|min:0',
        ]);
        $deal = HotelDeal::findOrFail($data['hotel_deal_id']);
        abort_unless($deal->hotel->author_id === auth()->id(), 403);

        $this->service->store($data);
        return redirect()->route('vendor.hotel-deal-supplements.index')->with('success', 'Supplement created.');
    }

    public function edit(HotelDealSupplement $hotelDealSupplement): View
    {
        abort_unless($hotelDealSupplement->deal->hotel->author_id === auth()->id(), 403);
        $deals = $this->vendorDeals();
        return view('vendor::hotel-deal-supplements.edit', compact('hotelDealSupplement', 'deals'));
    }

    public function update(Request $request, HotelDealSupplement $hotelDealSupplement): RedirectResponse
    {
        abort_unless($hotelDealSupplement->deal->hotel->author_id === auth()->id(), 403);
        $data = $request->validate([
            'hotel_deal_id' => 'required|exists:hotel_deals,id',
            'event_name'    => 'required|string|max:100',
            'date_start'    => 'required|date',
            'date_end'      => 'required|date|after_or_equal:date_start',
            'amount'        => 'required|numeric|min:0',
        ]);
        $this->service->update($hotelDealSupplement, $data);
        return redirect()->route('vendor.hotel-deal-supplements.index')->with('success', 'Supplement updated.');
    }

    public function destroy(HotelDealSupplement $hotelDealSupplement): RedirectResponse
    {
        abort_unless($hotelDealSupplement->deal->hotel->author_id === auth()->id(), 403);
        $this->service->delete($hotelDealSupplement);
        return back()->with('success', 'Supplement deleted.');
    }
}
