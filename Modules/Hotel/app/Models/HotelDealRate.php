<?php

namespace Modules\Hotel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotelDealRate extends Model
{
    protected $fillable = [
        'hotel_deal_id',
        'travel_date_start',
        'travel_date_end',
        'price_sgl_bb',
        'price_dbl_bb',
        'extra_bed_price',
        'child_price',
        'child_breakfast',
    ];

    protected $casts = [
        'travel_date_start' => 'date',
        'travel_date_end'   => 'date',
        'price_sgl_bb'      => 'decimal:2',
        'price_dbl_bb'      => 'decimal:2',
        'extra_bed_price'   => 'decimal:2',
        'child_price'       => 'decimal:2',
        'child_breakfast'   => 'decimal:2',
    ];

    public function deal(): BelongsTo
    {
        return $this->belongsTo(HotelDeal::class, 'hotel_deal_id');
    }

    /**
     * Calculate total price for a stay.
     * occupancy: 'sgl' | 'dbl'
     */
    public function calculateTotal(int $nights, string $occupancy = 'dbl', int $extraBeds = 0, int $children = 0): float
    {
        $basePrice = match ($occupancy) {
            'sgl'   => (float) $this->price_sgl_bb,
            default => (float) $this->price_dbl_bb,
        };

        $total = $basePrice * $nights;
        $total += ($this->extra_bed_price ?? 0) * $extraBeds * $nights;
        $total += ($this->child_price ?? 0) * $children * $nights;

        return round($total, 2);
    }
}
