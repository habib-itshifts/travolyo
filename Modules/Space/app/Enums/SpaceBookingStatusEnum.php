<?php

namespace Modules\Space\Enums;

enum SpaceBookingStatusEnum: string
{
    case Pending    = 'pending';
    case Confirmed  = 'confirmed';
    case CheckedIn  = 'checked_in';
    case CheckedOut = 'checked_out';
    case Cancelled  = 'cancelled';
}
