<?php

namespace Modules\Flight\DTOs;

use Modules\Flight\Enums\FlightProviderEnum;

class FlightOfferDto
{
    public function __construct(
        // Identity
        public readonly string         $offerId,
        public readonly FlightProviderEnum $provider,

        // Departure leg
        public readonly string         $origin,         // IATA  e.g. "DXB"
        public readonly string         $destination,    // IATA  e.g. "LHR"
        public readonly string         $departureAt,    // ISO 8601
        public readonly string         $arrivalAt,      // ISO 8601
        public readonly string         $duration,       // "5h 15m"
        public readonly int            $stops,          // 0 = direct

        // Price
        public readonly float          $totalAmount,
        public readonly string         $currency,

        // Airline (first/main carrier)
        public readonly string         $airlineName,
        public readonly string         $airlineCode,    // IATA code e.g. "EK"
        public readonly string         $airlineLogo,    // URL
        public readonly string         $flightNumber,

        // Cabin
        public readonly string         $cabinClass,

        // Segments – each leg's raw detail rows (for expanded card)
        public readonly array          $segments,

        // All legs (departure + optional return) for multi-leg display
        public readonly array          $rawFlightDetails,

        // Return leg (round-trip only)
        public readonly ?string        $returnDepartureAt = null,
        public readonly ?string        $returnArrivalAt   = null,
        public readonly ?string        $returnDuration    = null,
        public readonly int            $returnStops       = 0,

        // Misc
        public readonly bool           $isNextDay         = false,
        public readonly ?string        $badge             = null,  // 'cheapest'|'fastest'|'best_value'|null
    ) {}

    /** "Direct" | "1 stop" | "2 stops" */
    public function stopsLabel(): string
    {
        return match ($this->stops) {
            0       => 'Direct',
            1       => '1 stop',
            default => $this->stops . ' stops',
        };
    }
}
