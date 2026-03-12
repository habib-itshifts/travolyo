<?php

namespace Modules\Flight\DTOs;

use Modules\Flight\Enums\FlightProviderEnum;

class PayFlightDto
{
    /**
     * @param  FlightPassengerDto[]  $passengers
     */
    public function __construct(
        public readonly string         $offerId,
        public readonly FlightProviderEnum $provider,
        public readonly array          $passengers,
        public readonly string         $contactEmail,
        public readonly string         $contactPhone,
    ) {}
}
