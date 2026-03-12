<?php

namespace Modules\Flight\DTOs;

use Modules\Flight\Enums\FlightProviderEnum;

class SearchFlightDto
{
    public function __construct(
        public readonly string         $origin,          // IATA code
        public readonly string         $destination,     // IATA code
        public readonly string         $departureDate,   // Y-m-d
        public readonly int            $adults,
        public readonly string              $cabinClass,      // economy | business | first
        public readonly ?FlightProviderEnum $provider    = null, // null = search all providers
        public readonly ?string             $returnDate  = null, // null = one-way
        public readonly int            $children    = 0,
        public readonly int            $infants     = 0,
        public readonly ?string        $currency    = 'USD',
    ) {}
}
