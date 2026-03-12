<?php

namespace Modules\Flight\DTOs;

use Modules\Flight\Enums\FlightProviderEnum;

class CheckoutFlightDto
{
    /**
     * @param  FlightPassengerDto[]  $passengers
     */
    public function __construct(
        public readonly string         $offerId,
        public readonly FlightProviderEnum $provider,
        public readonly array          $passengers,
    ) {}
}
