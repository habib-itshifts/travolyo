<?php

namespace Modules\Flight\DTOs;

use Modules\Flight\Enums\FlightProviderEnum;

class PrebookFlightDto
{
    public function __construct(
        public readonly string         $offerId,
        public readonly FlightProviderEnum $provider,
        public readonly int            $adults,
        public readonly int            $children = 0,
        public readonly int            $infants  = 0,
    ) {}
}
