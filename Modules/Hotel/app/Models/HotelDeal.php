<?php

namespace Modules\Hotel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HotelDeal extends Model
{
    protected $fillable = [
        'hotel_id',
        'room_type_id',
        'release_period',
        'release_type',
        'booking_window',
        'cancellation_policy',
        'blackout_dates',
        'special_remarks',
        'travel_date_start',
        'travel_date_end',
        'price_sgl_bb',
        'price_dbl_bb',
        'extra_bed_price',
        'child_price',
        'child_breakfast',
        'status',
    ];

    protected $casts = [
        'release_period'   => 'integer',
        'booking_window'   => 'date',
        'blackout_dates'   => 'array',
        'travel_date_start' => 'date',
        'travel_date_end'   => 'date',
        'price_sgl_bb'     => 'decimal:2',
        'price_dbl_bb'     => 'decimal:2',
        'extra_bed_price'  => 'decimal:2',
        'child_price'      => 'decimal:2',
        'child_breakfast'  => 'decimal:2',
    ];

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Check if a given date falls in a blackout period.
     */
    public function isBlackedOut(string $date): bool
    {
        return in_array($date, $this->blackout_dates ?? []);
    }

    // Relationships
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function supplements(): HasMany
    {
        return $this->hasMany(HotelDealSupplement::class);
    }

    public function promoCodes(): BelongsToMany
    {
        return $this->belongsToMany(PromoCode::class, 'hotel_deal_promo_code')
            ->withTimestamps();
    }

    /**
     * Get total supplement amount for a date range.
     */
    public function supplementsForRange(string $checkIn, string $checkOut): float
    {
        return (float) $this->supplements()
            ->where('date_start', '<=', $checkOut)
            ->where('date_end', '>=', $checkIn)
            ->sum('amount');
    }
}
