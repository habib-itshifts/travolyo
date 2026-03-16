<?php

namespace Modules\Activity\Models;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityBookingPassenger extends Model
{
    protected $table = 'activity_booking_passengers';

    protected $fillable = [
        'booking_id',
        'activity_id',
        'title',
        'first_name',
        'last_name',
        'dob',
        'nationality',
        'gender',
        'passport_number',
        'passport_expiry_date',
        'contact_email',
        'contact_phone',
        'participants',
        'activity_date',
        'special_requests',
        'payment_gateway',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }
}
