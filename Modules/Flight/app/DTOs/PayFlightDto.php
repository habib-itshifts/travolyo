<?php

namespace Modules\Flight\DTOs;

use Modules\Flight\Enums\FlightProvider;

class PayFlightDto
{
    /**
     * @param  FlightPassengerDto[]  $passengers
     */
    public function __construct(
        public readonly string         $offerId,
        public readonly FlightProvider $provider,
        public readonly array          $passengers,
        public readonly string         $contactEmail,
        public readonly string         $contactPhone,
    ) {}
}
