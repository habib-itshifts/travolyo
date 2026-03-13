<?php

namespace Modules\Hotel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotelDealSupplement extends Model
{
    protected $fillable = [
        'hotel_deal_id',
        'event_name',
        'date_start',
        'date_end',
        'amount',
    ];

    protected $casts = [
        'date_start' => 'date',
        'date_end'   => 'date',
        'amount'     => 'decimal:2',
    ];

    public function deal(): BelongsTo
    {
        return $this->belongsTo(HotelDeal::class, 'hotel_deal_id');
    }

    /**
     * Check if this supplement applies to a given date.
     */
    public function appliesOn(string $date): bool
    {
        return $this->date_start->lte($date) && $this->date_end->gte($date);
    }
}
