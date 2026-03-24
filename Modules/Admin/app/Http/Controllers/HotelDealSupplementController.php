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
        $supplements = $this->service->list($request);
        $deals       = HotelDeal::with('hotel:id,name')->orderBy('id', 'desc')->get();

        return view('admin::hotel-deal-supplements.index', compact('supplements', 'deals'));
    }

    public function create(): View
    {
        $deals = HotelDeal::with('hotel:id,name')->orderBy('id', 'desc')->get();
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

        return redirect()->route('admin.hotel-deal-supplements.index')
            ->with('success', 'Supplement created successfully.');
    }

    public function edit(int $id): View
    {
        $supplement = HotelDealSupplement::findOrFail($id);
        $deals      = HotelDeal::with('hotel:id,name')->orderBy('id', 'desc')->get();

        return view('admin::hotel-deal-supplements.edit', compact('supplement', 'deals'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $supplement = HotelDealSupplement::findOrFail($id);

        $data = $request->validate([
            'hotel_deal_id' => 'required|exists:hotel_deals,id',
            'event_name'    => 'required|string|max:100',
            'date_start'    => 'required|date',
            'date_end'      => 'required|date|after_or_equal:date_start',
            'amount'        => 'required|numeric|min:0',
        ]);

        $this->service->update($supplement, $data);

        return redirect()->route('admin.hotel-deal-supplements.index')
            ->with('success', 'Supplement updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->service->delete(HotelDealSupplement::findOrFail($id));
        return back()->with('success', 'Supplement deleted.');
    }
}
