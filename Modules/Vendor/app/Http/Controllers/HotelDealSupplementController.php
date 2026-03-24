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

    public function index(Request $request): View
    {
        $supplements = $this->service->list($request, auth()->id());
        $deals       = HotelDeal::with('hotel:id,name')
            ->whereHas('hotel', fn ($q) => $q->where('author_id', auth()->id()))
            ->orderBy('id', 'desc')
            ->get();

        return view('vendor::hotel-deal-supplements.index', compact('supplements', 'deals'));
    }

    public function create(): View
    {
        $deals = HotelDeal::with('hotel:id,name')
            ->whereHas('hotel', fn ($q) => $q->where('author_id', auth()->id()))
            ->orderBy('id', 'desc')
            ->get();

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

        // Verify ownership
        HotelDeal::whereHas('hotel', fn ($q) => $q->where('author_id', auth()->id()))
            ->findOrFail($data['hotel_deal_id']);

        $this->service->store($data);

        return redirect()->route('vendor.hotel-deal-supplements.index')
            ->with('success', 'Supplement created successfully.');
    }

    public function edit(int $id): View
    {
        $supplement = HotelDealSupplement::whereHas('deal.hotel', fn ($q) => $q->where('author_id', auth()->id()))
            ->findOrFail($id);

        $deals = HotelDeal::with('hotel:id,name')
            ->whereHas('hotel', fn ($q) => $q->where('author_id', auth()->id()))
            ->orderBy('id', 'desc')
            ->get();

        return view('vendor::hotel-deal-supplements.edit', compact('supplement', 'deals'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $supplement = HotelDealSupplement::whereHas('deal.hotel', fn ($q) => $q->where('author_id', auth()->id()))
            ->findOrFail($id);

        $data = $request->validate([
            'hotel_deal_id' => 'required|exists:hotel_deals,id',
            'event_name'    => 'required|string|max:100',
            'date_start'    => 'required|date',
            'date_end'      => 'required|date|after_or_equal:date_start',
            'amount'        => 'required|numeric|min:0',
        ]);

        // Verify ownership of target deal
        HotelDeal::whereHas('hotel', fn ($q) => $q->where('author_id', auth()->id()))
            ->findOrFail($data['hotel_deal_id']);

        $this->service->update($supplement, $data);

        return redirect()->route('vendor.hotel-deal-supplements.index')
            ->with('success', 'Supplement updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $supplement = HotelDealSupplement::whereHas('deal.hotel', fn ($q) => $q->where('author_id', auth()->id()))
            ->findOrFail($id);

        $this->service->delete($supplement);
        return back()->with('success', 'Supplement deleted.');
    }
}
