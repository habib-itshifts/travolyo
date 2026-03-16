<?php

namespace Modules\Hotel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Admin\Models\MediaFile;

class HotelRoom extends Model
{
    protected $fillable = [
        'hotel_id',
        'name',
        'slug',
        'room_type',
        'currency',
        'image_id',
        'gallery',
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
        'currency'         => 'string',
        'image_id'         => 'integer',
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

    protected function mediaPathFromId(?int $id): ?string
    {
        if (! $id) {
            return null;
        }

        $relativePath = MediaFile::query()->whereKey($id)->value('file_path');

        return $relativePath ? 'uploads/' . ltrim($relativePath, '/') : null;
    }

    protected function parseGalleryIds(?string $value): array
    {
        return collect(explode(',', (string) $value))
            ->map(fn ($id) => (int) trim($id))
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->mediaPathFromId($this->image_id);
    }

    public function getGalleryUrlsAttribute(): array
    {
        return collect($this->parseGalleryIds($this->gallery))
            ->map(fn (int $id) => $this->mediaPathFromId($id))
            ->filter()
            ->values()
            ->all();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function featuredMedia(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'image_id');
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
