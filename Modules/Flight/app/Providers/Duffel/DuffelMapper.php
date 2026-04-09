<?php

namespace Modules\Flight\Providers\Duffel;

use Carbon\Carbon;
use Modules\Flight\DTOs\FlightOfferDto;
use Modules\Flight\Enums\FlightProviderEnum;

class DuffelMapper
{
    /**
     * Map a single raw Duffel offer array → FlightOfferDto.
     * Returns null if the offer is malformed (no slices/segments).
     */
    public function mapOffer(array $offer): ?FlightOfferDto
    {
        $slices = (array) ($offer['slices'] ?? []);
        if (empty($slices)) {
            return null;
        }

        $rawFlightDetails = [];

        foreach ($slices as $index => $slice) {
            $segments = array_values((array) ($slice['segments'] ?? []));
            if (empty($segments)) {
                continue;
            }

            $first = (array) $segments[0];
            $last  = (array) $segments[count($segments) - 1];

            $dep = $this->parseDateTime((string) ($first['departing_at'] ?? ''));
            $arr = $this->parseDateTime((string) ($last['arriving_at']  ?? ''));

            $carrier     = (array) ($first['operating_carrier'] ?? $first['marketing_carrier'] ?? []);
            $code        = strtoupper((string) ($carrier['iata_code'] ?? ''));
            $airlineName = (string) ($carrier['name'] ?? $code);
            $airlineLogo = (string) ($carrier['logo_symbol_url'] ?? (
                $code ? "https://www.gstatic.com/flights/airline_logos/70px/{$code}.png" : ''
            ));

            $rawFlightDetails[] = [
                'direction'     => $index === 0 ? 'Departure' : 'Return',
                'airline_name'  => $airlineName,
                'airline_logo'  => $airlineLogo,
                'airline_code'  => $code,
                'flight_number' => (string) ($first['operating_carrier_flight_number']
                                          ?? $first['marketing_carrier_flight_number'] ?? ''),
                'dep_iata'      => strtoupper((string) ($first['origin']['iata_code'] ?? '')),
                'dep_time'      => $dep?->format('H:i') ?? '',
                'dep_date'      => $dep?->format('d M, Y') ?? '',
                'arr_iata'      => strtoupper((string) ($last['destination']['iata_code'] ?? '')),
                'arr_time'      => $arr?->format('H:i') ?? '',
                'arr_date'      => $arr?->format('d M, Y') ?? '',
                'duration'      => $this->parseDuration((string) ($slice['duration'] ?? ''), $dep, $arr),
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

        return new FlightOfferDto(
            offerId:           (string) ($offer['id'] ?? ''),
            provider:          FlightProviderEnum::Duffel,
            origin:            $leg['dep_iata'],
            destination:       $leg['arr_iata'],
            departureAt:       $leg['dep_date'] . ' ' . $leg['dep_time'],
            arrivalAt:         $leg['arr_date'] . ' ' . $leg['arr_time'],
            duration:          $leg['duration'],
            stops:             $leg['stops'],
            totalAmount:       (float) ($offer['total_amount'] ?? 0),
            currency:          strtoupper((string) ($offer['total_currency'] ?? 'USD')),
            airlineName:       $leg['airline_name'],
            airlineCode:       $leg['airline_code'],
            airlineLogo:       $leg['airline_logo'],
            flightNumber:      $leg['flight_number'],
            cabinClass:        strtoupper((string) ($offer['slices'][0]['segments'][0]['passengers'][0]['cabin_class_marketing_name'] ?? 'ECONOMY')),
            segments:          $leg['segments'],
            passengers:        $this->mapPassengers($offer['passengers'] ?? []),
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
        $carrier = (array) ($segment['operating_carrier'] ?? $segment['marketing_carrier'] ?? []);

        $baggages = (array) ($segment['passengers'][0]['baggages'] ?? []);
        $checked  = collect($baggages)->where('type', 'checked')->sum('quantity');

        return [
            'from'          => strtoupper((string) ($segment['origin']['iata_code'] ?? '')),
            'to'            => strtoupper((string) ($segment['destination']['iata_code'] ?? '')),
            'dep_datetime'  => (string) ($segment['departing_at'] ?? ''),
            'arr_datetime'  => (string) ($segment['arriving_at']  ?? ''),
            'flight_number' => (string) ($segment['operating_carrier_flight_number']
                                       ?? $segment['marketing_carrier_flight_number'] ?? ''),
            'airline_code'  => strtoupper((string) ($carrier['iata_code'] ?? '')),
            'airline_name'  => (string) ($carrier['name'] ?? ''),
            'baggage'       => (string) (int) $checked,
            'cabin_class'   => strtoupper((string) ($segment['passengers'][0]['cabin_class_marketing_name'] ?? 'ECONOMY')),
        ];
    }

    private function mapPassengers(array $passengers): array
    {
        return array_values(array_map(function (array $pax, int $index) {
            return [
                'id' => (string) ($pax['id'] ?? ('passenger_' . $index)),
                'type' => (string) ($pax['type'] ?? 'adult'),
            ];
        }, $passengers, array_keys($passengers)));
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

    private function parseDuration(string $iso, ?Carbon $dep, ?Carbon $arr): string
    {
        // Try ISO 8601 duration first: PT5H15M
        if (preg_match('/PT(?:(\d+)H)?(?:(\d+)M)?/', $iso, $m)) {
            $h = (int) ($m[1] ?? 0);
            $m = (int) ($m[2] ?? 0);
            if ($h || $m) {
                return $h ? "{$h}h {$m}m" : "{$m}m";
            }
        }

        // Fall back to diff between dep/arr
        if ($dep && $arr) {
            $diff = $dep->diff($arr);
            $h    = $diff->h + ($diff->days * 24);
            return "{$h}h {$diff->i}m";
        }

        return '';
    }
}
