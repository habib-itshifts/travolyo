<?php

namespace Modules\Activity\DTOs;

use Modules\Activity\Enums\ActivityProviderEnum;

class PrebookActivityDto
{
    public function __construct(
        public readonly string               $offerId,       // activity id (DB or external)
        public readonly ActivityProviderEnum  $provider,
        public readonly string               $activityDate,
        public readonly int                  $participants,
        public readonly string               $currency = 'AED',
    ) {}
}
