<?php

namespace Modules\Hotel\Enums;

enum HotelStatusEnum: string
{
    case Draft     = 'draft';
    case Active    = 'active';
    case Inactive  = 'inactive';
    case Suspended = 'suspended';
}
