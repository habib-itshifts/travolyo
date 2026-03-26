<?php

namespace Modules\Hotel\Models;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingRoom extends Model
{
    protected $fillable = [
        'booking_id',
        'hotel_room_id',
        'hotel_deal_id',
        'check_in',
        'check_out',
        'nights',
        'adults',
        'children',
        'unit_price',
        'total_price',
        'extra_services',
        'special_requests',
        'status',
    ];

    protected $casts = [
        'check_in'       => 'date',
        'check_out'      => 'date',
        'nights'         => 'integer',
        'adults'         => 'integer',
        'children'       => 'integer',
        'unit_price'     => 'decimal:2',
        'total_price'    => 'decimal:2',
        'extra_services' => 'array',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(HotelRoom::class, 'hotel_room_id');
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(HotelDeal::class, 'hotel_deal_id');
    }
}
