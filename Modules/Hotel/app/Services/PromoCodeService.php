<?php

namespace Modules\Hotel\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Hotel\Models\PromoCode;

class PromoCodeService
{
    public function list(array $filters = [], ?int $userId = null): LengthAwarePaginator
    {
        return PromoCode::query()
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when($filters['search'] ?? null, fn ($q, $s) => $q->where('code', 'like', "%{$s}%")->orWhere('label', 'like', "%{$s}%"))
            ->when($filters['type'] ?? null, fn ($q, $t) => $q->where('type', $t))
            ->when(isset($filters['is_active']), fn ($q) => $q->where('is_active', $filters['is_active']))
            ->latest()
            ->paginate(20)
            ->withQueryString();
    }

    public function store(array $data): PromoCode
    {
        return PromoCode::create($data);
    }

    public function update(PromoCode $promoCode, array $data): PromoCode
    {
        $promoCode->update($data);
        return $promoCode;
    }

    public function delete(PromoCode $promoCode): void
    {
        $promoCode->delete();
    }

    public function getActiveForUser(?int $userId = null): \Illuminate\Database\Eloquent\Collection
    {
        return PromoCode::query()
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->active()
            ->orderBy('code')
            ->get();
    }
}
