<?php

namespace Modules\Space\Enums;

enum SpaceStatusEnum: string
{
    case Draft     = 'draft';
    case Active    = 'active';
    case Inactive  = 'inactive';
    case Suspended = 'suspended';
}
