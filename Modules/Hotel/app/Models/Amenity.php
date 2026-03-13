<?php

namespace Modules\Hotel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Amenity extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'category',
        'applies_to',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForHotels($query)
    {
        return $query->whereIn('applies_to', ['hotel', 'both']);
    }

    public function scopeForRooms($query)
    {
        return $query->whereIn('applies_to', ['room', 'both']);
    }

    // Relationships
    public function hotels(): BelongsToMany
    {
        return $this->belongsToMany(Hotel::class, 'hotel_amenity')
            ->withPivot('notes')
            ->withTimestamps();
    }

    public function hotelRooms(): BelongsToMany
    {
        return $this->belongsToMany(HotelRoom::class, 'hotel_room_amenity')
            ->withPivot('notes')
            ->withTimestamps();
    }
}
