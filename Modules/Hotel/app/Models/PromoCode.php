<?php

namespace Modules\Hotel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PromoCode extends Model
{
    protected $fillable = [
        'code',
        'label',
        'type',
        'description',
        'discount_type',
        'discount_value',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'valid_from'     => 'date',
        'valid_until'    => 'date',
        'is_active'      => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        return $query->active()
            ->where(fn ($q) => $q->whereNull('valid_from')->orWhere('valid_from', '<=', now()))
            ->where(fn ($q) => $q->whereNull('valid_until')->orWhere('valid_until', '>=', now()));
    }

    public function deals(): BelongsToMany
    {
        return $this->belongsToMany(HotelDeal::class, 'hotel_deal_promo_code')
            ->withTimestamps();
    }

    /**
     * Calculate discounted price.
     */
    public function applyTo(float $price): float
    {
        return match ($this->discount_type) {
            'percent' => round($price - ($price * $this->discount_value / 100), 2),
            'fixed'   => round(max(0, $price - $this->discount_value), 2),
            default   => $price,
        };
    }
}
