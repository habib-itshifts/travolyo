<?php

namespace Modules\Flight\Providers\TravolyoB2BXmlAgency;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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

class TravolyoB2BXmlAgencyProvider implements FlightProviderInterface
{
    private string  $baseUrl;
    private string  $apiKey;
    private int     $timeout;
    private int     $limit;
    private TravolyoB2BXmlAgencyMapper $mapper;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('travolyo_b2b.base_url', ''), '/');
        $this->apiKey  = (string) config('travolyo_b2b.auth_api_key', '');
        $this->timeout = (int)    config('travolyo_b2b.timeout', 20);
        $this->limit   = (int)    config('travolyo_b2b.default_limit', 20);
        $this->mapper  = new TravolyoB2BXmlAgencyMapper();
    }

    /** @return FlightOfferDto[] */
    public function search(SearchFlightDto $dto): array
    {
        if (empty($this->baseUrl) || empty($this->apiKey)) {
            throw FlightException::providerError('TravolyoB2B', 'API credentials not configured.');
        }

        $payload = [
            'origin'         => $dto->origin,
            'destination'    => $dto->destination,
            'departure_date' => $dto->departureDate,
            'trip_type'      => $dto->returnDate ? 'round_trip' : 'one_way',
            // 'cabin_class'    => $this->mapCabinClass($dto->cabinClass),
            'adults'         => $dto->adults,
            'children'       => $dto->children,
            'infants'        => $dto->infants,
        ];

        if ($dto->returnDate) {
            $payload['return_date'] = $dto->returnDate;
        }

        $response = $this->post('/api/v1/flights/search', $payload);

        Log::info('Flight search response', [
            'response' => $response->json()
        ]);


        $rawOffers = (array) ($response->json('offers') ?? []);

        return collect($rawOffers)
            ->take($this->limit)
            ->map(fn(array $offer) => $this->mapper->mapOffer($offer))
            ->filter()
            ->values()
            ->all();
    }

    public function prebook(PrebookFlightDto $dto): FlightOfferDto
    {
        [$offerCode, $searchGuid] = $this->splitOfferId($dto->offerId);

        $response = $this->post('/api/v1/flights/prebook', [
            'offer_code'  => $offerCode,
            'search_guid' => $searchGuid,
        ]);

        if (! $response->json('success')) {
            throw FlightException::providerError(
                'TravolyoB2B',
                (string) ($response->json('message') ?? 'Prebook failed.'),
            );
        }

        $data = (array) ($response->json('data') ?? []);

        // The prebook response returns the refreshed offer; map it if it has itineraries.
        if (! empty($data['itineraries'])) {
            $mapped = $this->mapper->mapOffer($data);
            if ($mapped) {
                return $mapped;
            }
        }

        throw FlightException::offerNotFound($dto->offerId);
    }

    public function checkout(CheckoutFlightDto $dto): FlightOfferDto
    {
        // B2B: checkout = re-prebook (confirm live price/availability)
        return $this->prebook(new PrebookFlightDto(
            offerId:  $dto->offerId,
            provider: FlightProviderEnum::TravolyoB2BXmlAgency,
            adults:   count(array_filter($dto->passengers, fn($p) => ($p->type ?? 'adult') === 'adult')),
            children: count(array_filter($dto->passengers, fn($p) => ($p->type ?? '') === 'child')),
            infants:  count(array_filter($dto->passengers, fn($p) => ($p->type ?? '') === 'infant')),
        ));
    }

    public function pay(PayFlightDto $dto): FlightOrderDto
    {
        [$offerCode, $searchGuid] = $this->splitOfferId($dto->offerId);

        $passengers = array_map(function ($pax) {
            return [
                'type'             => $pax->type ?? 'adult',
                'title'            => 'mr',
                'first_name'       => strtoupper($pax->firstName),
                'last_name'        => strtoupper($pax->lastName),
                'gender'           => strtolower($pax->gender) === 'f' ? 'F' : 'M',
                'date_of_birth'    => $pax->dateOfBirth,
                'nationality'      => strtoupper($pax->nationality ?? 'AE'),
                'passport_number'  => $pax->passportNumber,
                'passport_expiry'  => $pax->passportExpiry,
                'passport_country' => strtoupper($pax->passportCountry ?? 'AE'),
                'email'            => $pax->email,
                'phone'            => preg_replace('/[^0-9+]/', '', $pax->phone),
            ];
        }, $dto->passengers);

        $response = $this->post('/api/v1/flights/book', [
            'offer_code'    => $offerCode,
            'search_guid'   => $searchGuid,
            'contact_email' => $dto->contactEmail,
            'contact_phone' => preg_replace('/[^0-9+]/', '', $dto->contactPhone),
            'passengers'    => $passengers,
        ]);

        if (! $response->json('success')) {
            throw FlightException::providerError(
                'TravolyoB2B',
                (string) ($response->json('message') ?? 'Booking failed.'),
            );
        }

        return $this->mapOrder($response->json('data') ?? []);
    }

    public function getOrder(string $orderId): FlightOrderDto
    {
        $response = $this->get("/api/v1/flights/orders/{$orderId}");

        if (! $response->json('success')) {
            throw FlightException::orderNotFound($orderId);
        }

        return $this->mapOrder($response->json('data') ?? []);
    }

    public function cancelOrder(string $orderId): bool
    {
        $response = $this->post("/api/v1/flights/orders/{$orderId}/cancel", []);

        if (! $response->json('success')) {
            throw FlightException::providerError(
                'TravolyoB2B',
                (string) ($response->json('message') ?? 'Cancellation failed.'),
            );
        }

        return true;
    }

    // -------------------------------------------------------------------------

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
        $itineraries = (array) ($data['itineraries'] ?? []);
        $firstSeg    = (array) ($itineraries[0]['segments'][0] ?? []);
        $lastItin    = end($itineraries) ?: [];
        $lastSegs    = (array) ($lastItin['segments'] ?? []);
        $lastSeg     = (array) (end($lastSegs) ?: []);

        return new FlightOrderDto(
            orderId:           (string) ($data['booking_id'] ?? $data['order_id'] ?? ''),
            provider:          FlightProviderEnum::TravolyoB2BXmlAgency,
            status:            $this->mapOrderStatus((string) ($data['status'] ?? 'confirmed')),
            origin:            strtoupper((string) ($firstSeg['departure_airport'] ?? '')),
            destination:       strtoupper((string) ($lastSeg['arrival_airport'] ?? '')),
            departureAt:       (string) ($firstSeg['departure_time'] ?? ''),
            totalAmount:       (float) ($data['price_total'] ?? $data['total_amount'] ?? 0),
            currency:          strtoupper((string) ($data['currency'] ?? 'USD')),
            passengers:        (array) ($data['passengers'] ?? []),
            segments:          $itineraries,
            bookingReference:  (string) ($data['booking_reference'] ?? $data['pnr'] ?? ''),
            ticketingDeadline: (string) ($data['ticketing_deadline'] ?? '') ?: null,
        );
    }

    private function mapOrderStatus(string $status): FlightOrderStatusEnum
    {
        return match (strtolower($status)) {
            'pending'   => FlightOrderStatusEnum::Pending,
            'cancelled' => FlightOrderStatusEnum::Cancelled,
            'failed'    => FlightOrderStatusEnum::Failed,
            default     => FlightOrderStatusEnum::Confirmed,
        };
    }

    /**
     * Splits an offerId encoded as "{offer_code}::{search_guid}".
     *
     * @return array{0: string, 1: string}
     */
    private function splitOfferId(string $offerId): array
    {
        $parts = explode('::', $offerId, 2);
        return [$parts[0], $parts[1] ?? ''];
    }

    private function post(string $path, array $payload): \Illuminate\Http\Client\Response
    {
        $response = Http::withHeaders([
                'auth-api-key' => $this->apiKey,
                'Accept'       => 'application/json',
            ])
            ->timeout($this->timeout)
            ->post("{$this->baseUrl}{$path}", $payload);

        if (! $response->successful()) {
            throw FlightException::providerError('TravolyoB2B', $response->body(), $response->status());
        }

        return $response;
    }

    private function get(string $path): \Illuminate\Http\Client\Response
    {
        $response = Http::withHeaders([
                'auth-api-key' => $this->apiKey,
                'Accept'       => 'application/json',
            ])
            ->timeout($this->timeout)
            ->get("{$this->baseUrl}{$path}");

        if (! $response->successful()) {
            throw FlightException::providerError('TravolyoB2B', $response->body(), $response->status());
        }

        return $response;
    }
}
