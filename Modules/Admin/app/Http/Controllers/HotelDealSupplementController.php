<?php

namespace Modules\Admin\Http\Controllers;

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

    public function index(Request $request): View
    {
        $supplements = $this->service->list($request->only(['search', 'deal_id']));
        $deals = HotelDeal::with('hotel:id,name', 'roomType:id,name')->orderByDesc('id')->get();
        return view('admin::hotel-deal-supplements.index', compact('supplements', 'deals'));
    }

    public function create(): View
    {
        $deals = HotelDeal::with('hotel:id,name', 'roomType:id,name')->orderByDesc('id')->get();
        return view('admin::hotel-deal-supplements.create', compact('deals'));
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
        $this->service->store($data);
        return redirect()->route('admin.hotel-deal-supplements.index')->with('success', 'Supplement created.');
    }

    public function edit(HotelDealSupplement $hotelDealSupplement): View
    {
        $deals = HotelDeal::with('hotel:id,name', 'roomType:id,name')->orderByDesc('id')->get();
        return view('admin::hotel-deal-supplements.edit', compact('hotelDealSupplement', 'deals'));
    }

    public function update(Request $request, HotelDealSupplement $hotelDealSupplement): RedirectResponse
    {
        $data = $request->validate([
            'hotel_deal_id' => 'required|exists:hotel_deals,id',
            'event_name'    => 'required|string|max:100',
            'date_start'    => 'required|date',
            'date_end'      => 'required|date|after_or_equal:date_start',
            'amount'        => 'required|numeric|min:0',
        ]);
        $this->service->update($hotelDealSupplement, $data);
        return redirect()->route('admin.hotel-deal-supplements.index')->with('success', 'Supplement updated.');
    }

    public function destroy(HotelDealSupplement $hotelDealSupplement): RedirectResponse
    {
        $this->service->delete($hotelDealSupplement);
        return back()->with('success', 'Supplement deleted.');
    }
}
