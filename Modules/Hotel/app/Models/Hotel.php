<?php

namespace Modules\Hotel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Hotel extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'star_rating',
        'description',
        'short_description',
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
        'payment_methods',
        'languages_spoken',
        'status',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'star_rating'      => 'integer',
        'latitude'         => 'decimal:8',
        'longitude'        => 'decimal:8',
        'payment_methods'  => 'array',
        'languages_spoken' => 'array',
        'is_featured'      => 'boolean',
        'sort_order'       => 'integer',
    ];

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
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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
