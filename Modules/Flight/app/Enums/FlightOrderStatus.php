<?php

namespace Modules\Flight\Enums;

enum FlightOrderStatus: string
{
    case Pending   = 'pending';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';
    case Failed    = 'failed';
}
