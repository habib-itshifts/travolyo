<?php

namespace App\Http\Controllers;

use App\Enums\BookingObjectModelEnum;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Hotel\Actions\SearchHotelAction;
use Modules\Hotel\DTOs\SearchHotelDto;

class BookingController extends Controller
{
    public function show(string $code): View|RedirectResponse
    {
        $booking = Booking::with(['customer', 'vendor', 'author', 'payment'])
            ->where('code', $code)
            ->firstOrFail();

        $financials = $this->financialBreakdown($booking);

        // ── Flight ───────────────────────────────────────────────
        if ($booking->object_model === BookingObjectModelEnum::Flight->value) {
            $passengers = $booking->getJsonMeta('flight_passengers') ?: [];
            $orderRef   = $booking->getMeta('flight_pnr') ?: '';
            $hotelRecommendations = $this->recommendedHotelsForFlight($booking);

            return view('flight::flights.confirmation', compact('booking', 'passengers', 'orderRef', 'financials', 'hotelRecommendations'));
        }

        // ── Hotel ────────────────────────────────────────────────
        if ($booking->object_model === BookingObjectModelEnum::Hotel->value) {
            $hotel   = $booking->getJsonMeta('hotel_details');
            $gateway = $booking->getMeta('payment_gateway', '');

            // Calculate nights from actual dates — hotel_details doesn't store a 'nights' key
            $nights = (isset($hotel['check_in'], $hotel['check_out']))
                ? max(1, (int) Carbon::parse($hotel['check_in'])->diffInDays($hotel['check_out']))
                : 1;

            $bookingData = [
                'code'                  => $booking->code,
                'status'                => $booking->status,
                'payment_status'        => $booking->payment?->status ?? '',
                'hotel_name'            => $hotel['hotel_name'] ?? $hotel['name'] ?? '-',
                'hotel_address'         => $hotel['hotel_address'] ?? $hotel['address'] ?? '',
                'room_type'             => $hotel['room_name'] ?? $hotel['room_type'] ?? $hotel['room_type_name'] ?? '-',
                'meal_basis'            => $hotel['meal_basis'] ?? $hotel['meal_basis_name'] ?? '',
                'check_in'              => isset($hotel['check_in'])  ? Carbon::parse($hotel['check_in'])  : null,
                'check_out'             => isset($hotel['check_out']) ? Carbon::parse($hotel['check_out']) : null,
                'adults'                => (int) ($hotel['adults']   ?? 1),
                'children'              => (int) ($hotel['children'] ?? 0),
                'rooms'                 => (int) ($hotel['rooms']    ?? 1),
                'nights'                => $nights,
                'is_b2b'                => ($hotel['provider'] ?? 'local') !== 'local',
                'supplier_status'       => $hotel['supplier_status']       ?? '',
                'supplier_reference'    => $hotel['supplier_reference']    ?? '',
                'supplier_booking_code' => $hotel['supplier_booking_code'] ?? '',
                'gateway'               => $gateway,
                'price_per_night'       => (float) ($hotel['unit_price'] ?? 0),
                'subtotal'              => (float) ($hotel['subtotal']   ?? (($hotel['unit_price'] ?? 0) * $nights)),
                'taxes'                 => (float) ($hotel['taxes']      ?? 0),
                'total'                 => (float) ($booking->total      ?? $hotel['total_price'] ?? 0),
                'currency'              => strtoupper($booking->currency ?? 'USD'),
                'extra_price_items'     => $hotel['extra_price_items'] ?? [],
            ];

            return view('bookings.confirmation', compact('booking', 'bookingData', 'financials'));
        }

        // ── Activity ────────────────────────────────────────────────
        if ($booking->object_model === BookingObjectModelEnum::Activity->value) {
            return redirect()->route('activities.booking.detail', ['code' => $booking->code]);
        }

        // ── Fallback ─────────────────────────────────────────────
        return view('bookings.show', compact('booking', 'financials'));
    }

    private function financialBreakdown(Booking $booking): array
    {
        return [
            'currency'              => strtoupper($booking->currency ?? 'USD'),
            'total_before_discount' => $booking->total_before_discount,
            'coupon_amount'         => $booking->coupon_amount,
            'total_before_fees'     => $booking->total_before_fees,
            'buyer_fees'            => $booking->buyer_fees,
            'total'                 => $booking->total,
            'paid'                  => $booking->paid,
            'balance_due'           => $booking->balance_due,       // model accessor
            'commission_type'       => $booking->commission_type,
            'commission_rate'       => $booking->commission,
            'commission_amount'     => $booking->commission_amount,
            'vendor_service_fee'    => $booking->vendor_service_fee,
            'vendor_amount'         => $booking->vendor_amount,
            'platform_earnings'     => $booking->platform_earnings, // model accessor
            'vendor_paid_at'        => $booking->vendor_paid_at,
            'refund_status'         => $booking->refund_status,
            'refund_amount'         => $booking->refund_amount,
        ];
    }

    private function recommendedHotelsForFlight(Booking $booking): array
    {
        $flight = $booking->getJsonMeta('flight_details');
        $destinations = array_values(array_filter([
            $this->resolveAirportDestination((string) Arr::get($flight, 'arr_iata', '')),
            $this->resolveAirportDestination((string) Arr::get($flight, 'dep_iata', '')),
        ]));

        foreach ($destinations as $destination) {
            $hotels = $this->recommendedHotelCards($flight, $destination);

            if ($hotels !== []) {
                return [
                    'destination' => array_merge($destination, [
                        'search_url' => route('hotels.index', $this->buildHotelSearchParams($flight, $destination)),
                    ]),
                    'hotels' => $hotels,
                ];
            }
        }

        $fallbackDestination = $destinations[0] ?? [];

        return [
            'destination' => $fallbackDestination === []
                ? []
                : array_merge($fallbackDestination, [
                    'search_url' => route('hotels.index', $this->buildHotelSearchParams($flight, $fallbackDestination)),
                ]),
            'hotels' => [],
        ];
    }

    private function recommendedHotelCards(array $flight, array $destination): array
    {
        $searchParams = $this->buildHotelSearchParams($flight, $destination);
        $offers = (new SearchHotelAction())->handle(SearchHotelDto::fromArray($searchParams));

        return collect($offers)
            ->map(function ($offer) use ($destination, $searchParams) {
                $score = $this->hotelMatchScoreFromOffer($offer, $destination);

                if ($score <= 0) {
                    return null;
                }

                $images = collect($offer->images ?? [])
                    ->filter()
                    ->unique()
                    ->values();

                $cardSearchParams = array_merge($searchParams, [
                    'destination' => (string) ($offer->city ?: ($destination['city'] ?? $destination['country'] ?? '')),
                    'city' => (string) ($offer->city ?: ''),
                    'country' => (string) ($offer->country ?: ($destination['country'] ?? '')),
                    'provider' => $offer->provider->value,
                ]);

                return [
                    'score' => $score,
                    'is_featured' => ($offer->badge ?? null) === 'featured' || ($offer->sortBy ?? null) === 'featured',
                    'star_rating' => (int) $offer->starRating,
                    'sort_order' => 0,
                    'name' => $offer->name,
                    'address' => $offer->address,
                    'city' => $offer->city,
                    'country' => $offer->country,
                    'image_url' => $images->first(),
                    'amenities' => array_slice($offer->amenityNames ?? [], 0, 4),
                    'price' => (float) ($offer->lowestPrice ?? 0),
                    'currency' => strtoupper((string) ($offer->currency ?: session('currency', 'USD'))),
                    'provider' => $offer->provider->value,
                    'search_url' => route('hotels.index', $cardSearchParams),
                ];
            })
            ->filter()
            ->sort(function (array $left, array $right) {
                return [
                    $right['score'],
                    (int) $right['is_featured'],
                    $right['star_rating'],
                    -1 * $right['sort_order'],
                ] <=> [
                    $left['score'],
                    (int) $left['is_featured'],
                    $left['star_rating'],
                    -1 * $left['sort_order'],
                ];
            })
            ->take(3)
            ->map(function (array $hotel) {
                unset($hotel['score'], $hotel['is_featured'], $hotel['sort_order']);

                return $hotel;
            })
            ->values()
            ->all();
    }

    private function hotelMatchScoreFromOffer(object $offer, array $destination): int
    {
        $offerCity = strtolower(trim((string) ($offer->city ?? '')));
        $offerCountry = strtolower(trim((string) ($offer->country ?? '')));
        $offerCountryUpper = strtoupper(trim((string) ($offer->country ?? '')));
        $destinationCity = strtolower(trim((string) ($destination['city'] ?? '')));
        $countryCode = strtoupper(trim((string) ($destination['country_code'] ?? '')));
        $countryNames = collect($destination['country_names'] ?? [])
            ->map(fn ($value) => strtolower(trim((string) $value)))
            ->filter()
            ->all();

        if ($destinationCity !== '' && $offerCity === $destinationCity) {
            return 300;
        }

        if ($destinationCity !== '' && str_contains($offerCity, $destinationCity)) {
            return 240;
        }

        if ($countryCode !== '' && $offerCountryUpper === $countryCode) {
            return 180;
        }

        if (in_array($offerCountry, $countryNames, true)) {
            return 160;
        }

        return 0;
    }

    private function resolveAirportDestination(string $iataCode): array
    {
        $arrivalIata = strtoupper(trim($iataCode));

        if ($arrivalIata === '') {
            return [];
        }

        static $airports = null;

        if ($airports === null) {
            $file = public_path('data/worldwide-airports.json');

            if (! is_file($file)) {
                $airports = [];
            } else {
                $payload = json_decode((string) file_get_contents($file), true);

                $airports = collect(is_array($payload) ? $payload : [])
                    ->filter(fn ($airport) => ! empty($airport['IATA_CODE']))
                    ->mapWithKeys(fn ($airport) => [strtoupper((string) $airport['IATA_CODE']) => $airport])
                    ->all();
            }
        }

        $airport = $airports[$arrivalIata] ?? [];
        $city = trim((string) ($airport['CITY'] ?? ''));
        $countryCode = strtoupper(trim((string) ($airport['COUNTRY'] ?? '')));
        $countryNames = $this->countryNamesFromCode($countryCode);
        $countryLabel = $countryNames[0] ?? $countryCode;

        if ($city === '' && $countryCode === '') {
            return [];
        }

        return [
            'iata' => $arrivalIata,
            'city' => $city,
            'country_code' => $countryCode,
            'country' => $countryLabel,
            'country_names' => $countryNames,
            'label' => collect([$city, $countryLabel ?: $countryCode])->filter()->implode(', '),
        ];
    }

    private function countryNamesFromCode(string $countryCode): array
    {
        if ($countryCode === '') {
            return [];
        }

        $countryMap = Cache::remember('booking.hotel.country_map', 86400, function () {
            $file = public_path('data/top-cities-to-book.json');

            if (! is_file($file)) {
                return [];
            }

            $payload = json_decode((string) file_get_contents($file), true);
            $items = collect(Arr::get($payload, 'tabs', []))
                ->flatMap(fn (array $tab) => Arr::get($tab, 'regions', []))
                ->flatMap(fn (array $region) => Arr::get($region, 'items', []));

            return $items
                ->filter(fn ($item) => ! empty($item['country_code']) && ! empty($item['country']))
                ->groupBy(fn ($item) => strtoupper((string) $item['country_code']))
                ->map(fn ($group) => $group->pluck('country')->map(fn ($country) => trim((string) $country))->filter()->unique()->values()->all())
                ->all();
        });

        return collect($countryMap[$countryCode] ?? [])
            ->push($countryCode)
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function buildHotelSearchParams(array $flight, array $destination): array
    {
        $checkIn = $this->suggestedHotelCheckIn($flight);
        $checkOut = (clone $checkIn)->addDays(4);

        return [
            'destination' => (string) ($destination['city'] ?: $destination['country'] ?: $destination['country_code']),
            'country' => (string) ($destination['country'] ?? ''),
            'country_code' => (string) ($destination['country_code'] ?? ''),
            'location' => (string) ($destination['iata'] ?? ''),
            'city' => (string) ($destination['city'] ?? ''),
            'check_in' => $checkIn->toDateString(),
            'check_out' => $checkOut->toDateString(),
            'adults' => max(1, (int) Arr::get($flight, 'adults', 1)),
            'children' => max(0, (int) Arr::get($flight, 'children', 0)),
            'rooms' => 1,
        ];
    }

    private function suggestedHotelCheckIn(array $flight): Carbon
    {
        $date = (string) (Arr::get($flight, 'arr_date') ?: Arr::get($flight, 'dep_date') ?: now()->addDay()->toDateString());

        try {
            $parsed = Carbon::parse($date)->startOfDay();
        } catch (\Throwable) {
            $parsed = now()->addDay()->startOfDay();
        }

        return $parsed->isPast() ? now()->addDay()->startOfDay() : $parsed;
    }

}
