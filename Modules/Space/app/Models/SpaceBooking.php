<?php

namespace Modules\Space\Models;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpaceBooking extends Model
{
    protected $fillable = [
        'booking_id',
        'space_id',
        'check_in',
        'check_out',
        'nights',
        'guests',
        'price_per_night',
        'cleaning_fee',
        'service_fee',
        'total_price',
        'currency',
        'extra_services',
        'special_requests',
        'status',
    ];

    protected $casts = [
        'check_in'        => 'date',
        'check_out'       => 'date',
        'nights'          => 'integer',
        'guests'          => 'integer',
        'price_per_night' => 'decimal:2',
        'cleaning_fee'    => 'decimal:2',
        'service_fee'     => 'decimal:2',
        'total_price'     => 'decimal:2',
        'extra_services'  => 'array',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
    }
}
