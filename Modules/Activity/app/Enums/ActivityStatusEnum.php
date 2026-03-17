<?php

namespace Modules\Activity\Enums;

enum ActivityStatusEnum: string
{
    case Publish = 'publish';
    case Draft   = 'draft';
    case Pending = 'pending';
}
