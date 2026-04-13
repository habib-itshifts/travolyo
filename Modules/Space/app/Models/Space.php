<?php

namespace Modules\Space\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Admin\Models\MediaFile;
use Modules\Hotel\Models\Amenity;

class Space extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'author_id',
        'name',
        'slug',
        'description',
        'short_description',
        'type',
        'max_guests',
        'bedrooms',
        'bathrooms',
        'beds',
        'image_id',
        'banner_image_id',
        'gallery',
        'featured_image_url',
        'banner_image_url',
        'gallery_urls',
        'video_url',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'email',
        'phone',
        'check_in_time',
        'check_out_time',
        'min_day_before_booking',
        'min_stay_nights',
        'max_stay_nights',
        'house_rules',
        'cancellation_policy',
        'price_per_night',
        'sale_price',
        'cleaning_fee',
        'service_fee',
        'extra_prices',
        'currency',
        'status',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'max_guests'            => 'integer',
        'bedrooms'              => 'integer',
        'bathrooms'             => 'integer',
        'beds'                  => 'integer',
        'image_id'              => 'integer',
        'banner_image_id'       => 'integer',
        'latitude'              => 'decimal:8',
        'longitude'             => 'decimal:8',
        'gallery_urls'          => 'array',
        'house_rules'           => 'array',
        'extra_prices'          => 'array',
        'price_per_night'       => 'decimal:2',
        'sale_price'            => 'decimal:2',
        'cleaning_fee'          => 'decimal:2',
        'service_fee'           => 'decimal:2',
        'min_day_before_booking' => 'integer',
        'min_stay_nights'       => 'integer',
        'max_stay_nights'       => 'integer',
        'is_featured'           => 'boolean',
        'sort_order'            => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Media helpers (same pattern as Hotel model)
    // -------------------------------------------------------------------------

    protected function mediaPathFromId(?int $id): ?string
    {
        if (! $id) {
            return null;
        }

        $relativePath = MediaFile::query()->whereKey($id)->value('file_path');

        return $relativePath ? 'uploads/' . ltrim($relativePath, '/') : null;
    }

    protected function mediaIdFromStoredPath(?string $path): ?int
    {
        if (! $path) {
            return null;
        }

        $relativePath = ltrim((string) preg_replace('#^uploads/#', '', str_replace('\\', '/', $path)), '/');

        return MediaFile::query()->where('file_path', $relativePath)->value('id');
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

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    public function getFeaturedImageUrlAttribute($value): ?string
    {
        return $value ?: $this->mediaPathFromId($this->attributes['image_id'] ?? null);
    }

    public function getBannerImageUrlAttribute($value): ?string
    {
        return $value ?: $this->mediaPathFromId($this->attributes['banner_image_id'] ?? null);
    }

    public function getGalleryUrlsAttribute($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return array_values(array_filter($decoded));
            }
        }

        $ids = $this->parseGalleryIds($this->attributes['gallery'] ?? null);

        return collect($ids)
            ->map(fn (int $id) => $this->mediaPathFromId($id))
            ->filter()
            ->values()
            ->all();
    }

    public function getGalleryAttribute($value): ?string
    {
        if (! empty($value)) {
            return implode(',', $this->parseGalleryIds($value));
        }

        $paths = $this->getGalleryUrlsAttribute($this->attributes['gallery_urls'] ?? null);
        $ids = collect($paths)
            ->map(fn ($path) => $this->mediaIdFromStoredPath($path))
            ->filter()
            ->unique()
            ->values()
            ->all();

        return $ids ? implode(',', $ids) : null;
    }

    public function getHouseRulesAttribute($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        return $value ? (json_decode($value, true) ?? []) : [];
    }

    public function getExtraPricesAttribute($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        return $value ? (json_decode($value, true) ?? []) : [];
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function featuredMedia(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'image_id');
    }

    public function bannerMedia(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'banner_image_id');
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'space_amenity')
            ->withPivot('notes')
            ->withTimestamps();
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(SpaceAvailability::class);
    }

    public function spaceBookings(): HasMany
    {
        return $this->hasMany(SpaceBooking::class);
    }

    // -------------------------------------------------------------------------
    // Availability helpers
    // -------------------------------------------------------------------------

    /**
     * Check if the space is available for every date in the given range.
     * A date is considered unavailable if there's an explicit record with is_available=false
     * OR if there's an existing confirmed/pending booking overlapping those dates.
     */
    public function isAvailableForDates(string $checkIn, string $checkOut): bool
    {
        // Check explicit unavailability
        $blockedDates = $this->availabilities()
            ->where('is_available', false)
            ->where('date', '>=', $checkIn)
            ->where('date', '<', $checkOut)
            ->exists();

        if ($blockedDates) {
            return false;
        }

        // Check overlapping bookings (pending or confirmed)
        $overlapping = $this->spaceBookings()
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where('check_in', '<', $checkOut)
            ->where('check_out', '>', $checkIn)
            ->exists();

        return ! $overlapping;
    }

    /**
     * Get the effective price per night for a given date.
     * Returns price_override from availability if set, otherwise sale_price or price_per_night.
     */
    public function effectivePriceForDate(string $date): float
    {
        $override = $this->availabilities()
            ->where('date', $date)
            ->where('is_available', true)
            ->value('price_override');

        if ($override !== null) {
            return (float) $override;
        }

        return (float) ($this->sale_price ?: $this->price_per_night);
    }
}
