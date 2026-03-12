<?php

namespace App\Enums;

enum BookingObjectModelEnum: string
{
    case Flight = 'flight';
    case Hotel  = 'hotel';
    case Tour   = 'tour';
    case Car    = 'car';
}
