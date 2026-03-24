<?php

namespace Modules\Hotel\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Modules\Hotel\Models\PromoCode;

class PromoCodeService
{
    public function list(Request $request, ?int $userId = null): LengthAwarePaginator
    {
        return PromoCode::query()
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when($request->search, fn ($q) => $q->where(function ($q) use ($request) {
                $q->where('code', 'like', "%{$request->search}%")
                  ->orWhere('label', 'like', "%{$request->search}%");
            }))
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
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
}
