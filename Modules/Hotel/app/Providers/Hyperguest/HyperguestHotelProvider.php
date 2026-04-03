<?php

namespace Modules\Hotel\Providers\Hyperguest;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Modules\Hotel\DTOs\HotelOfferDto;
use Modules\Hotel\DTOs\HotelOrderDto;
use Modules\Hotel\DTOs\PrebookHotelDto;
use Modules\Hotel\DTOs\SearchHotelDto;
use Modules\Hotel\Enums\HotelProviderEnum;
use Modules\Hotel\Exceptions\HotelException;
use Modules\Hotel\Providers\HotelProviderInterface;

class HyperguestHotelProvider implements HotelProviderInterface
{
    private HyperguestHotelMapper $mapper;
    private string $bookingResponsePath;

    protected array $headers;

    public function __construct()
    {
        $this->mapper = new HyperguestHotelMapper();
        $this->bookingResponsePath = public_path('data/hyperguest/booking_response.json');
        $this->headers = [
            'Accept-Encoding' => 'gzip, deflate',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer 720c616825804c4498f1f21a1d128d4f',
        ];
    }

    public function search(SearchHotelDto $dto): array
    {
        $hotels = $this->loadHotels($dto);
        $nights = max($dto->nights(), 1);

        return collect($hotels)
            ->map(fn (array $hotel) => $this->mapper->toOfferDto($hotel, $nights, $dto->currency))
            ->filter(fn (HotelOfferDto $offer) => ! empty($offer->rooms))
            ->filter(fn (HotelOfferDto $offer) => $this->matchesPriceFilter($offer, $dto))
            ->values()
            ->all();
    }

    public function getRooms(
        string $offerId,
        string $cityCode,
        string $checkIn,
        string $checkOut,
        int $adults,
        int $children,
        string $currency = 'USD',
    ): array {
        $hotel = $this->findHotel($offerId, $checkIn, $checkOut, $adults, $children, $currency);

        $nights = max((int) Carbon::parse($checkIn)->diffInDays($checkOut), 1);

        return collect($hotel['rooms'] ?? [])
            ->flatMap(fn (array $room) => $this->mapper->toRoomOfferDtos($room, $nights, $currency, $hotel))
            ->values()
            ->all();
    }

    public function prebook(PrebookHotelDto $dto): HotelOfferDto
    {
        $keys = $this->mapper->decodeBookingKey($dto->roomId);

        $hotel = $this->findHotel(
            (string) ($keys['property_id'] ?? $keys['hotel_id']),
            $dto->checkIn,
            $dto->checkOut,
            $dto->adults,
            $dto->children,
            $dto->currency
        );

        $rawRoom = collect($hotel['rooms'] ?? [])
            ->firstWhere('roomId', $keys['room_id']);

        $ratePlan = collect($rawRoom['ratePlans'] ?? [])
            ->firstWhere('ratePlanCode', $keys['rate_code']);

        $isAvailable = ((int) ($rawRoom['numberOfAvailableRooms'] ?? 0)) > 0;

        if (! $rawRoom || ! $ratePlan || ! $isAvailable) {
            throw HotelException::roomUnavailable();
        }

        return $this->mapper->toOfferDto(
            $hotel,
            max((int) Carbon::parse($dto->checkIn)->diffInDays($dto->checkOut), 1),
            $dto->currency
        );
    }

    public function book(array $checkoutData, array $guest): array
    {
        $keys = $this->mapper->decodeBookingKey($checkoutData['room_id']);

        $payload = [
            'dates' => [
                'from' => $checkoutData['check_in'],
                'to' => $checkoutData['check_out'],
            ],
            'propertyId' => $keys['property_id'],
            'leadGuest' => [
                'birthDate' => $guest['birth_date'] ?? '1990-01-01',
                'contact' => [
                    'address' => $guest['address'] ?? 'N/A',
                    'city' => $guest['city'] ?? 'N/A',
                    'country' => $guest['country'] ?? 'N/A',
                    'email' => $guest['email'],
                    'phone' => $guest['phone'],
                    'state' => $guest['state'] ?? 'N/A',
                    'zip' => $guest['zip'] ?? 'N/A',
                ],
                'name' => [
                    'first' => $guest['first_name'],
                    'last' => $guest['last_name'],
                ],
                'title' => $guest['title'] ?? 'MR',
            ],
            'reference' => [
                'agency' => 'travolyo-' . uniqid(),
            ],
            'rooms' => [[
                'roomCode' => $keys['room_code'],
                'rateCode' => $keys['rate_code'],
                'expectedPrice' => [
                    'amount' => $checkoutData['total_price'],
                    'currency' => $checkoutData['currency'],
                ],
                'guests' => [[
                    'birthDate' => $guest['birth_date'] ?? '1990-01-01',
                    'name' => [
                        'first' => $guest['first_name'],
                        'last' => $guest['last_name'],
                    ],
                    'title' => $guest['title'] ?? 'MR',
                ]],
                'specialRequests' => $checkoutData['special_requests']
                    ? [$checkoutData['special_requests']]
                    : [],
            ]],
            'meta' => [
                ['key' => 'Source', 'value' => 'Travolyo'],
            ],
            'isTest' => true,
            'groupBooking' => false,
        ];

        $response = $this->loadBookingResponse();
        $response['reference'] = $payload['reference'];
        $response['content']['dates'] = $payload['dates'];
        $response['leadGuest']['name'] = $payload['leadGuest']['name'];
        $response['leadGuest']['contact']['email'] = $guest['email'];
        $response['leadGuest']['contact']['phone'] = $guest['phone'];

        if (! empty($response['rooms'][0])) {
            $response['rooms'][0]['roomCode'] = $keys['room_code'];
            $response['rooms'][0]['rateCode'] = $keys['rate_code'];
            $response['rooms'][0]['propertyId'] = $keys['property_id'];
        }

        $response['_request_payload'] = $payload;

        return $response;
    }

    public function getOrder(string $orderId): HotelOrderDto
    {
        $booking = \App\Models\Booking::with('meta')
            ->where('code', $orderId)
            ->where('object_model', 'hotel')
            ->firstOrFail();

        $meta = $booking->getMeta('hotel_details') ?? [];
        $hgData = $booking->getMeta('hyperguest_booking') ?? [];

        return new HotelOrderDto(
            orderId: $booking->code,
            provider: HotelProviderEnum::Hyperguest,
            hotelName: $meta['hotel_name'] ?? 'N/A',
            roomName: $meta['room_name'] ?? 'N/A',
            checkIn: $booking->start_date->toDateString(),
            checkOut: $booking->end_date->toDateString(),
            nights: $booking->start_date->diffInDays($booking->end_date),
            adults: $meta['adults'] ?? 1,
            children: $meta['children'] ?? 0,
            totalPrice: (float) $booking->total,
            currency: $booking->currency,
            status: $hgData['content']['status'] ?? $booking->status,
            guestFirstName: $booking->first_name,
            guestLastName: $booking->last_name,
            guestEmail: $booking->email,
            guestPhone: $booking->phone,
            specialRequests: $booking->customer_notes,
        );
    }

    public function cancelOrder(string $orderId): bool
    {
        $booking = \App\Models\Booking::where('code', $orderId)
            ->where('object_model', 'hotel')
            ->firstOrFail();

        $booking->update(['status' => 'cancelled']);

        return true;
    }

    private function loadHotels(SearchHotelDto $dto): array
    {
        $hotelIds = $this->findHotelIdsByDestination($dto->destination);

        if ($hotelIds === []) {
            return [];
        }

        $results = collect($this->searchHotelsByIds(
            $hotelIds,
            $dto->checkIn,
            $dto->checkOut,
            $dto->adults,
            $dto->children,
            $dto->currency,
            $dto->nationality,
        ));

        if ($dto->starRating !== null) {
            $results = $results->filter(
                fn (array $hotel) => (int) data_get($hotel, 'propertyInfo.starRating', 0) === $dto->starRating
            );
        }

        return $results->values()->all();
    }


    //Step::01 - return hotel ids of that specific destination
    private function findHotelIdsByDestination(string $destination): array
    {
        $destination = strtolower(trim($destination));
        $hotels = collect($this->loadStaticHotels());

        return $hotels
            ->filter(function (array $hotel) use ($destination) {
                $city = strtolower((string) ($hotel['city'] ?? $hotel['cityName'] ?? ''));
                $country = strtolower((string) ($hotel['country'] ?? $hotel['countryCode'] ?? ''));

                return $city === $destination || $country === $destination;
            })
            ->pluck('hotel_id')
            ->filter()
            ->map(fn ($id) => (string) $id)
            ->values()
            ->all();
    }

    /**
     * Fetch the full Hyperguest static hotel list.
     * The response is ~10MB so we cache it locally in storage/app/hyperguest_hotels.json
     * and only re-download when the cache is older than 1 hour.
     */
    private function loadStaticHotels(): array
    {
        $cachePath = storage_path('app/hyperguest_hotels.json');
        $ttl = 3600; // Refresh cache every 1 hour

        if (file_exists($cachePath) && (time() - filemtime($cachePath)) < $ttl) {
            return json_decode(file_get_contents($cachePath), true) ?? [];
        }

        // Fetch full hotel list from Hyperguest static endpoint
        $response = Http::withHeaders($this->headers)
            ->acceptJson()
            ->timeout(60)
            ->get('https://hg-static.hyperguest.com/hotels.json')
            ->throw();

        $payload = $response->json();

        // API may wrap hotels under a 'hotels' key or return a flat array
        $hotels = isset($payload['hotels']) && is_array($payload['hotels'])
            ? $payload['hotels']
            : (is_array($payload) ? $payload : []);

        // Save to cache file for next requests within the TTL window
        file_put_contents($cachePath, json_encode($hotels));

        return $hotels;
    }
    // Step:: 02 - find hotel details using
    private function searchHotelsByIds(
        array $hotelIds,
        string $checkIn,
        string $checkOut,
        int $adults,
        int $children,
        string $currency,
        string $nationality
    ): array {
        $nights = max((int) Carbon::parse($checkIn)->diffInDays($checkOut), 1);
        $guests = max($adults + $children, 1);

        // chunk(20) → each parallel request checks 20 hotels, no take() limit so all IDs are checked
        $chunks = collect($hotelIds)->chunk(20)->values();

        $responses = Http::pool(function ($pool) use ($chunks, $checkIn, $nights, $guests, $nationality, $currency) {
            return $chunks->map(fn ($chunk) => $pool
                ->withHeaders($this->headers)
                ->acceptJson()
                ->timeout(30)
                ->get('https://search-api.hyperguest.io/2.0/', [
                    'checkIn'             => $checkIn,
                    'nights'              => $nights,
                    'guests'              => $guests,
                    'hotelIds'            => $chunk->implode(','),
                    'customerNationality' => $nationality,
                    'currency'            => $currency,
                ])
            )->all();
        });

        return collect($responses)
            ->filter(fn ($r) => ! ($r instanceof \Throwable) && $r->successful())
            ->flatMap(fn ($r) => $this->unwrapResults($r->json()))
            ->filter(fn ($hotel) => is_array($hotel) && ! empty($hotel['propertyId']))
            ->values()
            ->all();
    }

    private function findHotel(
        string $offerId,
        string $checkIn,
        string $checkOut,
        int $adults,
        int $children,
        string $currency
    ): array {
        $results = $this->searchHotelsByIds(
            [(string) $offerId],
            $checkIn,
            $checkOut,
            $adults,
            $children,
            $currency,
            'AE',
        );

        $hotel = collect($results)->first(
            fn (array $result) => (string) ($result['propertyId'] ?? '') === (string) $offerId
        );

        if (! $hotel) {
            throw HotelException::notFound((int) $offerId);
        }

        return $hotel;
    }

    private function unwrapResults(mixed $payload): array
    {
        if (! is_array($payload)) {
            return [];
        }

        if (isset($payload['results']) && is_array($payload['results'])) {
            return $payload['results'];
        }

        return $payload;
    }

    private function matchesPriceFilter(HotelOfferDto $offer, SearchHotelDto $dto): bool
    {
        if ($dto->priceMin !== null && $offer->convertedLowestPrice < $dto->priceMin) {
            return false;
        }

        if ($dto->priceMax !== null && $offer->convertedLowestPrice > $dto->priceMax) {
            return false;
        }

        return true;
    }

    private function loadBookingResponse(): array
    {
        if (! is_file($this->bookingResponsePath)) {
            throw new \RuntimeException('Hyperguest booking response fixture not found.');
        }

        $decoded = json_decode((string) file_get_contents($this->bookingResponsePath), true);

        if (! is_array($decoded)) {
            throw new \RuntimeException('Hyperguest booking response fixture is invalid JSON.');
        }

        return $decoded;
    }
}
