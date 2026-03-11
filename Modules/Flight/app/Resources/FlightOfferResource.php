<?php

namespace Modules\Flight\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Flight\DTOs\FlightOfferDto;

/** @mixin FlightOfferDto */
class FlightOfferResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'             => $this->offerId,
            'provider'       => $this->provider->value,

            // Departure leg
            'origin'         => $this->origin,
            'destination'    => $this->destination,
            'departure_at'   => $this->departureAt,
            'arrival_at'     => $this->arrivalAt,
            'duration'       => $this->duration,
            'stops'          => $this->stops,
            'stops_label'    => $this->stopsLabel(),
            'is_next_day'    => $this->isNextDay,

            // Airline
            'airline_name'   => $this->airlineName,
            'airline_code'   => $this->airlineCode,
            'airline_logo'   => $this->airlineLogo,
            'flight_number'  => $this->flightNumber,

            // Price
            'total_amount'   => $this->totalAmount,
            'currency'       => $this->currency,

            // Cabin
            'cabin_class'    => $this->cabinClass,

            // Segments detail
            'segments'       => $this->segments,
            'flight_details' => $this->rawFlightDetails,

            // Return leg (round-trip only)
            'return_departure_at' => $this->returnDepartureAt,
            'return_arrival_at'   => $this->returnArrivalAt,
            'return_duration'     => $this->returnDuration,
            'return_stops'        => $this->returnStops,

            // Badge
            'badge'          => $this->badge,
        ];
    }
}
