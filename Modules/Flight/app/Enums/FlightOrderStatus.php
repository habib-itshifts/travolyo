<?php

namespace Modules\Flight\Enums;

enum FlightOrderStatusEnum: string
{
    case Pending   = 'pending';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';
    case Failed    = 'failed';
}
