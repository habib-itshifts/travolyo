<?php

namespace Modules\Flight\Providers\Duffel;

use Illuminate\Support\Facades\Http;
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
        $this->timeout    = (int)    config('duffel.timeout', 20);
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
            ->timeout($this->timeout)
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
        $passengers = array_map(function ($pax, $idx) {
            return [
                'id'          => (string) $idx,
                'title'       => 'mr',
                'gender'      => strtolower($pax->gender) === 'f' ? 'f' : 'm',
                'given_name'  => strtoupper($pax->firstName),
                'family_name' => strtoupper($pax->lastName),
                'born_on'     => $pax->dateOfBirth,
                'email'       => $pax->email,
                'phone_number'=> preg_replace('/[^0-9+]/', '', $pax->phone),
                'passport_number'         => $pax->passportNumber,
                'passport_expiry_date'    => $pax->passportExpiry,
                'passport_issuance_country' => strtoupper($pax->passportCountry ?? 'AE'),
                'nationality'             => strtoupper($pax->nationality ?? 'AE'),
            ];
        }, $dto->passengers, array_keys($dto->passengers));

        $response = Http::withToken($this->token)
            ->withHeaders([
                'Duffel-Version' => $this->apiVersion,
                'Accept'         => 'application/json',
            ])
            ->timeout($this->timeout)
            ->post("{$this->baseUrl}/air/orders", [
                'data' => [
                    'type'              => 'instant',
                    'selected_offers'   => [$dto->offerId],
                    'passengers'        => $passengers,
                    'payments'          => [[
                        'type'     => 'balance',
                        'amount'   => '0',
                        'currency' => 'USD',
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
            ->timeout($this->timeout)
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
            ->timeout($this->timeout)
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

        $passengers = array_fill(0, max(1, $dto->adults), ['type' => 'adult']);
        for ($i = 0; $i < $dto->children; $i++) { $passengers[] = ['type' => 'child']; }
        for ($i = 0; $i < $dto->infants;  $i++) { $passengers[] = ['type' => 'infant_without_seat']; }

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
        $lastSeg  = (array) (end($slices[0]['segments'] ?? [[] ]) ?: []);

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

    private function get(string $path): \Illuminate\Http\Client\Response
    {
        $response = Http::withToken($this->token)
            ->withHeaders([
                'Duffel-Version' => $this->apiVersion,
                'Accept'         => 'application/json',
            ])
            ->timeout($this->timeout)
            ->get("{$this->baseUrl}{$path}");

        if (! $response->successful()) {
            throw FlightException::providerError('Duffel', $response->body(), $response->status());
        }

        return $response;
    }
}
