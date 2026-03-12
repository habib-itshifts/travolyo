<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingMeta extends Model
{
    protected $table    = 'booking_meta';
    protected $fillable = ['booking_id', 'name', 'val'];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
