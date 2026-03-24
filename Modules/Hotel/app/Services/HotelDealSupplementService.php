<?php

namespace Modules\Hotel\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Modules\Hotel\Models\HotelDealSupplement;

class HotelDealSupplementService
{
    public function list(Request $request, ?int $userId = null): LengthAwarePaginator
    {
        return HotelDealSupplement::query()
            ->with(['deal.hotel'])
            ->when($userId, fn ($q) => $q->whereHas('deal.hotel', fn ($q) => $q->where('author_id', $userId)))
            ->when($request->search, fn ($q) => $q->where('event_name', 'like', "%{$request->search}%"))
            ->when($request->deal_id, fn ($q) => $q->where('hotel_deal_id', $request->deal_id))
            ->latest()
            ->paginate(20)
            ->withQueryString();
    }

    public function store(array $data): HotelDealSupplement
    {
        return HotelDealSupplement::create($data);
    }

    public function update(HotelDealSupplement $supplement, array $data): HotelDealSupplement
    {
        $supplement->update($data);
        return $supplement;
    }

    public function delete(HotelDealSupplement $supplement): void
    {
        $supplement->delete();
    }
}
