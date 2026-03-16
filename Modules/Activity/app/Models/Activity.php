<?php

namespace Modules\Activity\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Admin\Models\MediaFile;

class Activity extends Model
{
    use SoftDeletes;

    public const CATEGORY_OPTIONS = [
        'Adventure',
        'Sightseeing',
        'Cultural',
        'Water Sports',
        'Family',
        'City Tour',
        'Desert',
        'Cruise',
    ];

    protected $table = 'activities';

    protected $fillable = [
        'title',
        'slug',
        'category',
        'city',
        'country',
        'address',
        'price_per_person',
        'currency',
        'max_participants',
        'duration',
        'instant_confirmation',
        'is_active',
        'email_flyer_enabled',
        'description',
        'extra_information',
        'gallery',
        'image_id',
        'author_id',
        'status',
        'create_user',
        'update_user',
    ];

    protected $casts = [
        'price_per_person' => 'decimal:2',
        'max_participants' => 'integer',
        'instant_confirmation' => 'boolean',
        'is_active' => 'boolean',
        'email_flyer_enabled' => 'boolean',
        'extra_information' => 'array',
        'image_id' => 'integer',
        'author_id' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'image_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'create_user');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'update_user');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'publish')->where('is_active', true);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image?->url;
    }

    public function getGalleryIdsAttribute(): array
    {
        return collect(explode(',', (string) $this->gallery))
            ->map(fn ($id) => (int) trim($id))
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    public function getGalleryUrlsAttribute(): array
    {
        $ids = $this->gallery_ids;

        if (empty($ids)) {
            return [];
        }

        $mediaItems = MediaFile::query()->whereIn('id', $ids)->get()->keyBy('id');

        return collect($ids)
            ->map(fn ($id) => $mediaItems->get($id)?->url)
            ->filter()
            ->values()
            ->all();
    }
}
