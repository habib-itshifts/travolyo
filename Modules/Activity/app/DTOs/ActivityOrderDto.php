<?php

namespace Modules\Activity\DTOs;

use Modules\Activity\Enums\ActivityProviderEnum;

class ActivityOrderDto
{
    public function __construct(
        public readonly string               $orderId,       // booking code e.g. TRV-2026-ABC123
        public readonly ActivityProviderEnum  $provider,
        public readonly string               $activityTitle,
        public readonly ?string              $category,
        public readonly ?string              $city,
        public readonly string               $activityDate,
        public readonly int                  $participants,
        public readonly ?string              $duration,
        public readonly float                $unitPrice,
        public readonly float                $totalPrice,
        public readonly string               $currency,
        public readonly string               $status,
        public readonly string               $contactEmail,
        public readonly string               $contactPhone,
        public readonly ?string              $guestFirstName = null,
        public readonly ?string              $guestLastName  = null,
        public readonly ?string              $specialRequests = null,
        public readonly array                $passengers      = [],
    ) {}
}
