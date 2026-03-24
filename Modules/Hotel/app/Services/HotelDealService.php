<?php

namespace Modules\Hotel\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelDeal;
use Modules\Hotel\Models\HotelDealRate;

class HotelDealService
{
    public function list(Hotel $hotel, array $filters = []): LengthAwarePaginator
    {
        return $hotel->deals()
            ->with(['roomType', 'rates'])
            ->when($filters['status'] ?? null, fn ($q, $s) => $q->where('status', $s))
            ->when($filters['room_type_id'] ?? null, fn ($q, $rt) => $q->where('room_type_id', $rt))
            ->latest()
            ->paginate(20)
            ->withQueryString();
    }

    public function store(array $data, array $rates = []): HotelDeal
    {
        $data['blackout_dates'] = $this->normalizeBlackoutDates($data['blackout_dates'] ?? null);
        $deal = HotelDeal::create($data);
        $this->syncRates($deal, $rates);
        return $deal;
    }

    public function update(HotelDeal $deal, array $data, array $rates = []): HotelDeal
    {
        $data['blackout_dates'] = $this->normalizeBlackoutDates($data['blackout_dates'] ?? null);
        $deal->update($data);
        $this->syncRates($deal, $rates);
        return $deal;
    }

    public function delete(HotelDeal $deal): void
    {
        $deal->delete();
    }

    protected function syncRates(HotelDeal $deal, array $rates): void
    {
        $deal->rates()->delete();

        foreach ($rates as $rate) {
            if (empty($rate['travel_date_start']) || empty($rate['travel_date_end'])) {
                continue;
            }
            $deal->rates()->create([
                'travel_date_start' => $rate['travel_date_start'],
                'travel_date_end'   => $rate['travel_date_end'],
                'price_sgl_bb'      => $rate['price_sgl_bb'] ?? null,
                'price_dbl_bb'      => $rate['price_dbl_bb'] ?? null,
                'extra_bed_price'   => $rate['extra_bed_price'] ?? null,
                'child_price'       => $rate['child_price'] ?? null,
                'child_breakfast'   => $rate['child_breakfast'] ?? null,
            ]);
        }
    }

    protected function normalizeBlackoutDates(array|string|null $dates): ?array
    {
        if (is_string($dates)) {
            $dates = array_filter(array_map('trim', explode(',', $dates)));
        }
        return !empty($dates) ? array_values($dates) : null;
    }
}
