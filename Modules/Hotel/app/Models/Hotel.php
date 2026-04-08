<?php

namespace Modules\Hotel\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Admin\Models\MediaFile;

class Hotel extends Model
{
    use SoftDeletes;

    protected static function booted(): void
    {
        // External hotels are cached locally to avoid repeated API calls for static data (name, location, images).
        // Room availability and pricing are always fetched live as they change frequently.
        static::addGlobalScope('exclude_external', static function (Builder $builder): void {
            $builder->where('is_external', false);
        });
    }

    protected $fillable = [
        'author_id',
        'name',
        'slug',
        'scraping_url',
        'star_rating',
        'description',
        'short_description',
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
        'website',
        'check_in_time',
        'check_out_time',
        'base_price',
        'sale_price',
        'min_day_before_booking',
        'min_day_stays',
        'policies',
        'nearby_places',
        'extra_prices',
        'related_hotel_ids',
        'currency',
        'payment_methods',
        'languages_spoken',
        'status',
        'is_featured',
        'sort_order',
        'source',
        'external_id',
        'is_external',
        'external_data',
        'external_synced_at',
    ];

    protected $casts = [
        'star_rating'      => 'integer',
        'scraping_url'     => 'string',
        'image_id'         => 'integer',
        'banner_image_id'  => 'integer',
        'latitude'         => 'decimal:8',
        'longitude'        => 'decimal:8',
        'gallery_urls'     => 'array',
        'base_price'       => 'decimal:2',
        'sale_price'       => 'decimal:2',
        'min_day_before_booking' => 'integer',
        'min_day_stays'    => 'integer',
        'policies'         => 'array',
        'nearby_places'    => 'array',
        'extra_prices'     => 'array',
        'currency'         => 'string',
        'payment_methods'  => 'array',
        'languages_spoken' => 'array',
        'is_featured'         => 'boolean',
        'sort_order'          => 'integer',
        'is_external'    => 'boolean',
        'external_data'       => 'array',
        'external_synced_at'  => 'datetime',
    ];

    protected function mediaIdFromStoredPath(?string $path): ?int
    {
        if (! $path) {
            return null;
        }

        $relativePath = ltrim((string) preg_replace('#^uploads/#', '', str_replace('\\', '/', $path)), '/');

        return MediaFile::query()->where('file_path', $relativePath)->value('id');
    }

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

    public function getImageIdAttribute($value): ?int
    {
        return $value ? (int) $value : $this->mediaIdFromStoredPath($this->attributes['featured_image_url'] ?? null);
    }

    public function getBannerImageIdAttribute($value): ?int
    {
        return $value ? (int) $value : $this->mediaIdFromStoredPath($this->attributes['banner_image_url'] ?? null);
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

    public function getVideoAttribute(): ?string
    {
        return $this->video_url;
    }

    public function setVideoAttribute(?string $value): void
    {
        $this->attributes['video_url'] = $value;
    }

    public function getPolicyAttribute(): array
    {
        return $this->policies ?? [];
    }

    public function setPolicyAttribute(array|string|null $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = is_array($decoded) ? $decoded : [];
        }

        $this->attributes['policies'] = json_encode(array_values((array) $value));
    }

    public function getSurroundingAttribute(): array
    {
        return $this->nearby_places ?? [];
    }

    public function setSurroundingAttribute(array|string|null $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = is_array($decoded) ? $decoded : [];
        }

        $this->attributes['nearby_places'] = json_encode(array_values((array) $value));
    }

    public function getExtraPriceAttribute(): array
    {
        return $this->extra_prices ?? [];
    }

    public function setExtraPriceAttribute(array|string|null $value): void
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = is_array($decoded) ? $decoded : [];
        }

        $this->attributes['extra_prices'] = json_encode(array_values((array) $value));
    }

    public function getRelatedIdsAttribute(): ?string
    {
        return $this->related_hotel_ids;
    }

    public function setRelatedIdsAttribute(?string $value): void
    {
        $this->attributes['related_hotel_ids'] = $value;
    }

    public function getPriceAttribute(): ?string
    {
        return $this->base_price;
    }

    public function setPriceAttribute(float|int|string|null $value): void
    {
        $this->attributes['base_price'] = $value;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }


    // Relationships
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

    public function rooms(): HasMany
    {
        return $this->hasMany(HotelRoom::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'hotel_amenity')
            ->withPivot('notes')
            ->withTimestamps();
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'hotel_service')
            ->withPivot(['is_available', 'price', 'price_type', 'notes'])
            ->withTimestamps();
    }

    public function deals(): HasMany
    {
        return $this->hasMany(HotelDeal::class);
    }

    public function publishedDeals(): HasMany
    {
        return $this->hasMany(HotelDeal::class)->where('status', 'published');
    }
}
