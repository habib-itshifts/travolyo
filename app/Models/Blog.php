<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Admin\Models\MediaFile;

class Blog extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'status',
        'image_id',
        'author_id',
        'cat_id',
        'create_user',
        'update_user',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'cat_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(BlogTag::class, 'blog_tag', 'blog_id', 'tag_id')->withTimestamps();
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
        return $query->where('status', 'publish');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image?->url;
    }
}
