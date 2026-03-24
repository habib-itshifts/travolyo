<?php

namespace Modules\Hotel\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelDeal;

class HotelDealService
{
    public function list(Hotel $hotel, Request $request): LengthAwarePaginator
    {
        return $hotel->deals()
            ->with(['room', 'rates'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();
    }

    public function store(Hotel $hotel, array $data, bool $isVendor = false): HotelDeal
    {
        if ($isVendor) {
            $data['status'] = 'draft';
        }

        $rates      = $data['rates'] ?? [];
        $promoCodes = $data['promo_codes'] ?? [];
        unset($data['rates'], $data['promo_codes']);

        $deal = $hotel->deals()->create($data);

        if (!empty($rates)) {
            $deal->rates()->createMany($rates);
        }

        if (!empty($promoCodes)) {
            $deal->promoCodes()->sync($promoCodes);
        }

        return $deal->load(['room', 'rates', 'promoCodes']);
    }

    public function update(HotelDeal $deal, array $data, bool $isVendor = false): HotelDeal
    {
        if ($isVendor) {
            $data['status'] = 'draft';
        }

        $rates      = $data['rates'] ?? [];
        $promoCodes = $data['promo_codes'] ?? [];
        unset($data['rates'], $data['promo_codes']);

        $deal->update($data);

        // Sync rates: delete old, create new
        $deal->rates()->delete();
        if (!empty($rates)) {
            $deal->rates()->createMany($rates);
        }

        if (isset($promoCodes)) {
            $deal->promoCodes()->sync($promoCodes);
        }

        return $deal->load(['room', 'rates', 'promoCodes']);
    }

    public function delete(HotelDeal $deal): void
    {
        $deal->delete();
    }
}
