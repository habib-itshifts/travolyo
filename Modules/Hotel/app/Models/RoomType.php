<?php

namespace Modules\Hotel\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Admin\Models\MediaFile;

class RoomType extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'image_id',
        'bed_configuration',
        'max_adults',
        'max_children',
        'max_occupancy',
        'size_sqm',
        'view_type',
        'description',
        'price_sgl_bb',
        'price_dbl_bb',
        'extra_bed_price',
        'child_price',
        'child_breakfast',
        'extra_adult_price',
        'extra_child_price',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'image_id'          => 'integer',
        'bed_configuration' => 'array',
        'max_adults'        => 'integer',
        'max_children'      => 'integer',
        'max_occupancy'     => 'integer',
        'size_sqm'          => 'decimal:2',
        'price_sgl_bb'      => 'decimal:2',
        'price_dbl_bb'      => 'decimal:2',
        'extra_bed_price'   => 'decimal:2',
        'child_price'       => 'decimal:2',
        'child_breakfast'   => 'decimal:2',
        'extra_adult_price' => 'decimal:2',
        'extra_child_price' => 'decimal:2',
        'is_active'         => 'boolean',
        'sort_order'        => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function featuredMedia(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'image_id');
    }

    public function hotelRooms(): HasMany
    {
        return $this->hasMany(HotelRoom::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'room_type_amenity')
            ->withPivot('notes')
            ->withTimestamps();
    }

    public function deals(): HasMany
    {
        return $this->hasMany(HotelDeal::class);
    }
}
