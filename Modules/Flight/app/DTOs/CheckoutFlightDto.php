<?php

namespace Modules\Flight\DTOs;

use Modules\Flight\Enums\FlightProvider;

class CheckoutFlightDto
{
    /**
     * @param  FlightPassengerDto[]  $passengers
     */
    public function __construct(
        public readonly string         $offerId,
        public readonly FlightProvider $provider,
        public readonly array          $passengers,
    ) {}
}
