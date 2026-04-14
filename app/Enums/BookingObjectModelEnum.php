<?php

namespace App\Enums;

enum BookingObjectModelEnum: string
{
    case Flight   = 'flight';
    case Hotel    = 'hotel';
    case Activity = 'activity';
    case Space    = 'space';
    case Tour     = 'tour';
    case Car      = 'car';
}
