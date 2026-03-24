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
        'booking_window',
        'cancellation_policy',
        'max_occupancy_label',
        'allocation',
        'blackout_dates',
        'special_remarks',
        'status',
    ];

    protected $casts = [
        'blackout_dates' => 'array',
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

    public function rates(): HasMany
    {
        return $this->hasMany(HotelDealRate::class);
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
     * Get the applicable rate for a given check-in date.
     */
    public function rateForDate(string $date): ?HotelDealRate
    {
        return $this->rates()
            ->where('travel_date_start', '<=', $date)
            ->where('travel_date_end', '>=', $date)
            ->first();
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
