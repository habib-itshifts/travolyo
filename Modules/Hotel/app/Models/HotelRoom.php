<?php

namespace Modules\Hotel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HotelRoom extends Model
{
    protected $fillable = [
        'hotel_id',
        'name',
        'slug',
        'room_type',
        'bed_configuration',
        'max_adults',
        'max_children',
        'max_occupancy',
        'size_sqm',
        'floor',
        'view_type',
        'description',
        'base_price',
        'extra_adult_price',
        'extra_child_price',
        'quantity',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'bed_configuration' => 'array',
        'max_adults'        => 'integer',
        'max_children'      => 'integer',
        'max_occupancy'     => 'integer',
        'size_sqm'          => 'decimal:2',
        'base_price'        => 'decimal:2',
        'extra_adult_price' => 'decimal:2',
        'extra_child_price' => 'decimal:2',
        'quantity'          => 'integer',
        'is_active'         => 'boolean',
        'sort_order'        => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'hotel_room_amenity')
            ->withPivot('notes')
            ->withTimestamps();
    }

    public function bookingRooms(): HasMany
    {
        return $this->hasMany(BookingRoom::class);
    }

    /**
     * Calculate the total price for a stay.
     */
    public function calculatePrice(int $nights, int $adults, int $children): float
    {
        $base = $this->base_price * $nights;
        $extraAdults = max(0, $adults - 2) * $this->extra_adult_price * $nights;
        $extraChildren = $children * $this->extra_child_price * $nights;

        return round($base + $extraAdults + $extraChildren, 2);
    }
}
