<?php

namespace Modules\Hotel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Service extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'category',
        'description',
        'is_chargeable',
        'default_price',
        'price_type',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_chargeable'  => 'boolean',
        'default_price'  => 'decimal:2',
        'is_active'      => 'boolean',
        'sort_order'     => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function hotels(): BelongsToMany
    {
        return $this->belongsToMany(Hotel::class, 'hotel_service')
            ->withPivot(['is_available', 'price', 'price_type', 'notes'])
            ->withTimestamps();
    }
}
