<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BlogTag extends Model
{
    use SoftDeletes;

    protected $table = 'blog_tags';

    protected $fillable = [
        'name',
        'slug',
        'content',
        'create_user',
        'update_user',
    ];

    public function blogs(): BelongsToMany
    {
        return $this->belongsToMany(Blog::class, 'blog_tag', 'tag_id', 'blog_id')->withTimestamps();
    }

    public static function saveNames(string $names, ?int $userId = null): array
    {
        $items = collect(explode(',', $names))
            ->map(fn ($name) => trim($name))
            ->filter()
            ->unique(function ($name) {
                return Str::lower($name);
            });

        $ids = [];

        foreach ($items as $name) {
            $slug = Str::slug($name);
            $tag = static::query()->firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'content' => null,
                    'create_user' => $userId,
                    'update_user' => $userId,
                ]
            );

            if (! $tag->wasRecentlyCreated) {
                $tag->update([
                    'update_user' => $userId,
                ]);
            }

            $ids[] = $tag->id;
        }

        return array_values(array_unique($ids));
    }
}
