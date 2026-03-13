<?php

namespace Modules\Hotel\Enums;

enum BookingRoomStatusEnum: string
{
    case Pending    = 'pending';
    case Confirmed  = 'confirmed';
    case CheckedIn  = 'checked_in';
    case CheckedOut = 'checked_out';
    case Cancelled  = 'cancelled';
}
