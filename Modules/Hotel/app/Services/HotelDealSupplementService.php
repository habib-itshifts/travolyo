<?php

namespace Modules\Hotel\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Hotel\Models\HotelDealSupplement;

class HotelDealSupplementService
{
    public function list(array $filters = [], ?int $userId = null): LengthAwarePaginator
    {
        return HotelDealSupplement::query()
            ->with(['deal.hotel', 'deal.roomType'])
            ->when($userId, fn ($q) => $q->whereHas('deal.hotel', fn ($hq) => $hq->where('author_id', $userId)))
            ->when($filters['search'] ?? null, fn ($q, $s) => $q->where('event_name', 'like', "%{$s}%"))
            ->when($filters['deal_id'] ?? null, fn ($q, $d) => $q->where('hotel_deal_id', $d))
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
