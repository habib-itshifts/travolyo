<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TopDestination extends Model
{
    protected $fillable = [
        'city',
        'country',
        'country_code',
        'location',
        'image_path',
        'image_alt',
        'accommodations_label',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'image_url',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getImageUrlAttribute(): string
    {
        $path = trim((string) $this->image_path);

        if ($path === '') {
            return asset('images/banner.jpg');
        }

        if (Str::startsWith($path, ['http://', 'https://', '//', 'data:'])) {
            return $path;
        }

        if (! Str::startsWith($path, ['assets/', 'uploads/'])) {
            $path = 'uploads/' . ltrim($path, '/');
        }

        return asset(ltrim($path, '/'));
    }
}
