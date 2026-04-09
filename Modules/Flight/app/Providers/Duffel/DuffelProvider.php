<?php

namespace Modules\Flight\Providers\Duffel;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Modules\Flight\DTOs\CheckoutFlightDto;
use Modules\Flight\DTOs\FlightOfferDto;
use Modules\Flight\DTOs\FlightOrderDto;
use Modules\Flight\DTOs\PayFlightDto;
use Modules\Flight\DTOs\PrebookFlightDto;
use Modules\Flight\DTOs\SearchFlightDto;
use Modules\Flight\Enums\FlightOrderStatusEnum;
use Modules\Flight\Enums\FlightProviderEnum;
use Modules\Flight\Exceptions\FlightException;
use Modules\Flight\Providers\FlightProviderInterface;

class DuffelProvider implements FlightProviderInterface
{
    private const REQUEST_TIMEOUT = 300;
    private string $baseUrl;
    private string $token;
    private string $apiVersion;
    private int    $timeout;
    private int    $limit;
    private DuffelMapper $mapper;

    public function __construct()
    {
        $this->baseUrl    = rtrim((string) config('duffel.base_url', 'https://api.duffel.com'), '/');
        $this->token      = (string) config('duffel.token', '');
        $this->apiVersion = (string) config('duffel.api_version', 'v2');
        $this->timeout    = (int)    config('duffel.timeout', 300);
        $this->limit      = (int)    config('duffel.default_limit', 20);
        $this->mapper     = new DuffelMapper();
    }

    /** @return FlightOfferDto[] */
    public function search(SearchFlightDto $dto): array
    {
        if (empty($this->token)) {
            throw FlightException::providerError('Duffel', 'API token not configured.');
        }

        $response = Http::withToken($this->token)
            ->withHeaders([
                'Duffel-Version' => $this->apiVersion,
                'Accept'         => 'application/json',
            ])
            ->timeout(self::REQUEST_TIMEOUT)
            ->post("{$this->baseUrl}/air/offer_requests?return_offers=true", $this->buildSearchPayload($dto));

        if (! $response->successful()) {
            throw FlightException::providerError('Duffel', $response->body(), $response->status());
        }

        $rawOffers = (array) ($response->json('data.offers') ?? []);

        return collect($rawOffers)
            ->take($this->limit)
            ->map(fn(array $offer) => $this->mapper->mapOffer($offer))
            ->filter()
            ->values()
            ->all();
    }

    public function prebook(PrebookFlightDto $dto): FlightOfferDto
    {
        $response = $this->get("/air/offers/{$dto->offerId}");

        $mapped = $this->mapper->mapOffer($response->json('data') ?? []);
        if (! $mapped) {
            throw FlightException::offerNotFound($dto->offerId);
        }

        return $mapped;
    }

    public function checkout(CheckoutFlightDto $dto): FlightOfferDto
    {
        // Duffel: checkout is same as prebook (re-fetch offer to confirm price/availability)
        return $this->prebook(new PrebookFlightDto(
            offerId:  $dto->offerId,
            provider: FlightProviderEnum::Duffel,
        ));
    }

    public function pay(PayFlightDto $dto): FlightOrderDto
    {
        $offer = $this->prebook(new PrebookFlightDto(
            offerId: $dto->offerId,
            provider: FlightProviderEnum::Duffel,
        ));

        $rawOfferResponse = $this->get("/air/offers/{$dto->offerId}");
        $rawPassengers = (array) ($rawOfferResponse->json('data.passengers') ?? []);

        $duffelPassengerIds = array_values(array_filter(array_map(
            fn (array $pax) => (string) ($pax['id'] ?? ''),
            $rawPassengers
        )));

        if (empty($duffelPassengerIds)) {
            throw FlightException::providerError('Duffel', 'Offer passenger IDs are missing. Please search again and try booking from a fresh offer.');
        }

        $passengers = array_map(function ($pax, $index) use ($dto, $duffelPassengerIds) {
            $bornOn = $this->normalizeBirthDate($pax->dateOfBirth, $pax->type ?? 'adult');
            $phone = $this->normalizePhoneNumber($pax->phone ?: $dto->contactPhone);

            $data = [
                'id'                        => $duffelPassengerIds[$index] ?? null,
                'title'                     => strtolower((string) ($pax->title ?? 'mr')),
                'given_name'                => $pax->firstName,
                'family_name'               => $pax->lastName,
                'email'                     => $pax->email,
                'phone_number'              => $phone,
                'born_on'                   => $bornOn,
                'gender'                    => strtolower((string) ($pax->gender ?? 'm')),
                'passport_number'           => $pax->passportNumber,
                'passport_expiry_date'      => $pax->passportExpiry,
                'passport_issuance_country' => strtoupper($pax->passportCountry ?: $pax->nationality ?: 'AE'),
                'nationality'               => strtoupper($pax->nationality ?? 'AE'),
            ];

            return array_filter($data, fn ($v) => $v !== null && $v !== '');
        }, $dto->passengers, array_keys($dto->passengers));

        $amount = number_format((float) $offer->totalAmount, 2, '.', '');

        $response = Http::withToken($this->token)
            ->withHeaders([
                'Duffel-Version' => $this->apiVersion,
                'Accept'         => 'application/json',
            ])
            ->timeout(self::REQUEST_TIMEOUT)
            ->post("{$this->baseUrl}/air/orders", [
                'data' => [
                    'type'              => 'instant',
                    'selected_offers'   => [$dto->offerId],
                    'passengers'        => $passengers,
                    'payments'          => [[
                        'type'     => 'balance',
                        'amount'   => $amount,
                        'currency' => $offer->currency,
                    ]],
                ],
            ]);

        if (! $response->successful()) {
            throw FlightException::providerError('Duffel', $response->body(), $response->status());
        }

        return $this->mapOrder($response->json('data') ?? []);
    }

    public function getOrder(string $orderId): FlightOrderDto
    {
        $response = $this->get("/air/orders/{$orderId}");
        return $this->mapOrder($response->json('data') ?? []);
    }

    public function cancelOrder(string $orderId): bool
    {
        $response = Http::withToken($this->token)
            ->withHeaders([
                'Duffel-Version' => $this->apiVersion,
                'Accept'         => 'application/json',
            ])
            ->timeout(self::REQUEST_TIMEOUT)
            ->post("{$this->baseUrl}/air/order_cancellations", [
                'data' => ['order_id' => $orderId],
            ]);

        if (! $response->successful()) {
            throw FlightException::providerError('Duffel', $response->body(), $response->status());
        }

        // Confirm cancellation
        $cancellationId = $response->json('data.id');
        Http::withToken($this->token)
            ->withHeaders(['Duffel-Version' => $this->apiVersion, 'Accept' => 'application/json'])
            ->timeout(self::REQUEST_TIMEOUT)
            ->post("{$this->baseUrl}/air/order_cancellations/{$cancellationId}/actions/confirm");

        return true;
    }

    // -------------------------------------------------------------------------

    private function buildSearchPayload(SearchFlightDto $dto): array
    {
        $slices = [[
            'origin'         => $dto->origin,
            'destination'    => $dto->destination,
            'departure_date' => $dto->departureDate,
        ]];

        if ($dto->returnDate) {
            $slices[] = [
                'origin'         => $dto->destination,
                'destination'    => $dto->origin,
                'departure_date' => $dto->returnDate,
            ];
        }

        $passengers = [];
        $passengerIndex = 0;

        for ($i = 0; $i < max(1, $dto->adults); $i++) {
            $passengers[] = [
                'id'   => 'passenger_' . $passengerIndex++,
                'type' => 'adult',
            ];
        }

        for ($i = 0; $i < $dto->children; $i++) {
            $passengers[] = [
                'id'   => 'passenger_' . $passengerIndex++,
                'type' => 'child',
            ];
        }

        for ($i = 0; $i < $dto->infants; $i++) {
            $passengers[] = [
                'id'   => 'passenger_' . $passengerIndex++,
                'type' => 'infant_without_seat',
            ];
        }

        return [
            'data' => [
                'slices'      => $slices,
                'passengers'  => $passengers,
                'cabin_class' => $this->mapCabinClass($dto->cabinClass),
            ],
        ];
    }

    private function mapCabinClass(string $class): string
    {
        return match (strtoupper($class)) {
            'PREMIUM_ECONOMY' => 'premium_economy',
            'BUSINESS'        => 'business',
            'FIRST'           => 'first',
            default           => 'economy',
        };
    }

    private function mapOrder(array $data): FlightOrderDto
    {
        $slices   = (array) ($data['slices'] ?? []);
        $first    = (array) ($slices[0]['segments'][0] ?? []);
        $segments = (array) ($slices[0]['segments'] ?? []);
        $lastSeg  = $segments ? (array) end($segments) : [];

        return new FlightOrderDto(
            orderId:           (string) ($data['id'] ?? ''),
            provider:          FlightProviderEnum::Duffel,
            status:            FlightOrderStatusEnum::Confirmed,
            origin:            strtoupper((string) ($first['origin']['iata_code'] ?? '')),
            destination:       strtoupper((string) ($lastSeg['destination']['iata_code'] ?? '')),
            departureAt:       (string) ($first['departing_at'] ?? ''),
            totalAmount:       (float)  ($data['total_amount'] ?? 0),
            currency:          strtoupper((string) ($data['total_currency'] ?? 'USD')),
            passengers:        (array)  ($data['passengers'] ?? []),
            segments:          $slices,
            bookingReference:  (string) ($data['booking_reference'] ?? ''),
            ticketingDeadline: (string) ($data['payment_required_by'] ?? null),
        );
    }

    private function normalizeBirthDate(string $value, string $type = 'adult'): string
    {
        $today = Carbon::today();
        $fallback = match (strtolower($type)) {
            'child' => $today->copy()->subYears(10),
            'infant', 'infant_without_seat' => $today->copy()->subMonths(6),
            default => $today->copy()->subYears(30),
        };

        try {
            $date = Carbon::parse(trim($value));
            if ($date->isFuture() || $date->greaterThan($today)) {
                return $fallback->toDateString();
            }

            return $date->toDateString();
        } catch (\Throwable) {
            return $fallback->toDateString();
        }
    }

    private function normalizePhoneNumber(string $value): string
    {
        $phone = preg_replace('/[^0-9+]/', '', trim($value));

        if ($phone === '') {
            return '+971000000000';
        }

        if (! str_starts_with($phone, '+')) {
            $phone = '+' . ltrim($phone, '+');
        }

        return $phone;
    }

    private function get(string $path): \Illuminate\Http\Client\Response
    {
        $response = Http::withToken($this->token)
            ->withHeaders([
                'Duffel-Version' => $this->apiVersion,
                'Accept'         => 'application/json',
            ])
            ->timeout(self::REQUEST_TIMEOUT)
            ->get("{$this->baseUrl}{$path}");

        if (! $response->successful()) {
            throw FlightException::providerError('Duffel', $response->body(), $response->status());
        }

        return $response;
    }
}
