<?php

namespace Modules\Flight\Providers\TravolyoB2BXmlAgency;

use Carbon\Carbon;
use Modules\Flight\DTOs\FlightOfferDto;
use Modules\Flight\Enums\FlightProviderEnum;

class TravolyoB2BXmlAgencyMapper
{
    /**
     * Map a single raw B2B offer array → FlightOfferDto.
     * Returns null if the offer is malformed (no itineraries/segments).
     *
     * The offerId is encoded as "{offer_code}::{search_guid}" so that
     * the provider can split it back for the prebook/book calls.
     */
    public function mapOffer(array $offer): ?FlightOfferDto
    {
        $itineraries = array_values((array) ($offer['itineraries'] ?? []));
        if (empty($itineraries)) {
            return null;
        }

        $rawFlightDetails = [];

        foreach ($itineraries as $index => $itinerary) {
            $segments = array_values((array) ($itinerary['segments'] ?? []));
            if (empty($segments)) {
                continue;
            }

            $first = (array) $segments[0];
            $last  = (array) $segments[count($segments) - 1];

            $dep  = $this->parseDateTime((string) ($first['departure_time'] ?? ''));
            $arr  = $this->parseDateTime((string) ($last['arrival_time'] ?? ''));
            $code = strtoupper((string) ($first['carrier_code'] ?? $first['carrier_name'] ?? ''));

            $rawFlightDetails[] = [
                'direction'     => $index === 0 ? 'Departure' : 'Return',
                'airline_name'  => (string) ($first['carrier_name'] ?? $code),
                'airline_logo'  => $code ? "https://www.gstatic.com/flights/airline_logos/70px/{$code}.png" : '',
                'airline_code'  => $code,
                'flight_number' => (string) ($first['flight_number'] ?? ''),
                'dep_iata'      => strtoupper((string) ($first['departure_airport'] ?? '')),
                'dep_time'      => $dep?->format('H:i') ?? '',
                'dep_date'      => $dep?->format('d M, Y') ?? '',
                'arr_iata'      => strtoupper((string) ($last['arrival_airport'] ?? '')),
                'arr_time'      => $arr?->format('H:i') ?? '',
                'arr_date'      => $arr?->format('d M, Y') ?? '',
                'duration'      => $this->parseDuration((string) ($itinerary['duration'] ?? ''), $dep, $arr),
                'stops'         => max(0, count($segments) - 1),
                'is_next_day'   => $dep && $arr ? $dep->toDateString() !== $arr->toDateString() : false,
                'segments'      => array_map(fn($s) => $this->mapSegment((array) $s), $segments),
            ];
        }

        if (empty($rawFlightDetails)) {
            return null;
        }

        $leg    = $rawFlightDetails[0];
        $retLeg = $rawFlightDetails[1] ?? null;

        $offerCode  = (string) ($offer['offer_code'] ?? $offer['id'] ?? '');
        $searchGuid = (string) ($offer['search_guid'] ?? '');

        return new FlightOfferDto(
            offerId:           "{$offerCode}::{$searchGuid}",
            provider:          FlightProviderEnum::TravolyoB2BXmlAgency,
            origin:            $leg['dep_iata'],
            destination:       $leg['arr_iata'],
            departureAt:       $leg['dep_date'] . ' ' . $leg['dep_time'],
            arrivalAt:         $leg['arr_date'] . ' ' . $leg['arr_time'],
            duration:          $leg['duration'],
            stops:             $leg['stops'],
            totalAmount:       (float) ($offer['price_total'] ?? 0),
            currency:          strtoupper((string) ($offer['currency'] ?? 'USD')),
            airlineName:       $leg['airline_name'],
            airlineCode:       $leg['airline_code'],
            airlineLogo:       $leg['airline_logo'],
            flightNumber:      $leg['flight_number'],
            cabinClass:        strtoupper((string) ($itineraries[0]['segments'][0]['cabin'] ?? 'ECONOMY')),
            segments:          $leg['segments'],
            rawFlightDetails:  $rawFlightDetails,
            returnDepartureAt: $retLeg ? $retLeg['dep_date'] . ' ' . $retLeg['dep_time'] : null,
            returnArrivalAt:   $retLeg ? $retLeg['arr_date'] . ' ' . $retLeg['arr_time'] : null,
            returnDuration:    $retLeg['duration'] ?? null,
            returnStops:       $retLeg['stops'] ?? 0,
            isNextDay:         $leg['is_next_day'],
        );
    }

    // -------------------------------------------------------------------------

    private function mapSegment(array $segment): array
    {
        return [
            'from'          => strtoupper((string) ($segment['departure_airport'] ?? '')),
            'to'            => strtoupper((string) ($segment['arrival_airport'] ?? '')),
            'dep_datetime'  => (string) ($segment['departure_time'] ?? ''),
            'arr_datetime'  => (string) ($segment['arrival_time'] ?? ''),
            'flight_number' => (string) ($segment['flight_number'] ?? ''),
            'airline_code'  => strtoupper((string) ($segment['carrier_code'] ?? '')),
            'airline_name'  => (string) ($segment['carrier_name'] ?? ''),
            'baggage'       => (string) ($segment['baggage_count'] ?? '0'),
            'cabin_class'   => strtoupper((string) ($segment['cabin'] ?? 'ECONOMY')),
        ];
    }

    private function parseDateTime(string $value): ?Carbon
    {
        if (empty($value)) {
            return null;
        }
        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private function parseDuration(string $raw, ?Carbon $dep, ?Carbon $arr): string
    {
        // ISO 8601: PT5H15M
        if (preg_match('/PT(?:(\d+)H)?(?:(\d+)M)?/', $raw, $m)) {
            $h = (int) ($m[1] ?? 0);
            $min = (int) ($m[2] ?? 0);
            if ($h || $min) {
                return $h ? "{$h}h {$min}m" : "{$min}m";
            }
        }

        // Plain "5:15" or "315" (minutes) format
        if (preg_match('/^(\d+):(\d{2})$/', $raw, $m)) {
            return "{$m[1]}h {$m[2]}m";
        }
        if (ctype_digit($raw) && (int) $raw > 0) {
            $h   = intdiv((int) $raw, 60);
            $min = (int) $raw % 60;
            return $h ? "{$h}h {$min}m" : "{$min}m";
        }

        // Fall back to dep→arr diff
        if ($dep && $arr) {
            $diff = $dep->diff($arr);
            $h    = $diff->h + ($diff->days * 24);
            return "{$h}h {$diff->i}m";
        }

        return '';
    }
}
