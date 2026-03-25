<?php

namespace Modules\Hotel\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelDeal;

class HotelDealService
{
    public function list(Hotel $hotel, array $filters = []): LengthAwarePaginator
    {
        return $hotel->deals()
            ->with(['roomType'])
            ->when($filters['status'] ?? null, fn ($q, $s) => $q->where('status', $s))
            ->when($filters['room_type_id'] ?? null, fn ($q, $rt) => $q->where('room_type_id', $rt))
            ->latest()
            ->paginate(20)
            ->withQueryString();
    }

    public function store(array $data): HotelDeal
    {
        $data['blackout_dates'] = $this->normalizeBlackoutDates($data['blackout_dates'] ?? null);
        return HotelDeal::create($data);
    }

    public function update(HotelDeal $deal, array $data): HotelDeal
    {
        $data['blackout_dates'] = $this->normalizeBlackoutDates($data['blackout_dates'] ?? null);
        $deal->update($data);
        return $deal;
    }

    public function delete(HotelDeal $deal): void
    {
        $deal->delete();
    }

    protected function normalizeBlackoutDates(array|string|null $dates): ?array
    {
        if (is_string($dates)) {
            $dates = array_filter(array_map('trim', explode(',', $dates)));
        }
        return !empty($dates) ? array_values($dates) : null;
    }
}
