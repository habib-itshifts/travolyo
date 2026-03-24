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
        'room_type_id',
        'image_id',
        'gallery',
        'floor',
        'quantity',
        'base_price',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'room_type_id'  => 'integer',
        'image_id'      => 'integer',
        'base_price'    => 'decimal:2',
        'quantity'      => 'integer',
        'is_active'     => 'boolean',
        'sort_order'    => 'integer',
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

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
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
     * Extra pricing comes from the room type.
     */
    public function calculatePrice(int $nights, int $adults, int $children): float
    {
        $base = $this->base_price * $nights;
        $extraAdults = max(0, $adults - 2) * ($this->roomType->extra_adult_price ?? 0) * $nights;
        $extraChildren = $children * ($this->roomType->extra_child_price ?? 0) * $nights;

        return round($base + $extraAdults + $extraChildren, 2);
    }
}
