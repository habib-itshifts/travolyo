<?php

namespace Modules\Hotel\Providers\TravolyoB2B;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Hotel\DTOs\HotelOfferDto;
use Modules\Hotel\DTOs\HotelOrderDto;
use Modules\Hotel\DTOs\PrebookHotelDto;
use Modules\Hotel\DTOs\SearchHotelDto;
use Modules\Hotel\Enums\HotelProviderEnum;
use Modules\Hotel\Exceptions\HotelException;
use Modules\Hotel\Providers\HotelProviderInterface;

class TravolyoB2BHotelProvider implements HotelProviderInterface
{
    protected string $baseUrl;
    protected array $headers;
    protected int $searchTimeout;
    protected int $connectTimeout;
    private TravolyoB2BHotelMapper $mapper;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('travolyo_b2b.base_url', ''), '/');
        $this->headers = [
            'auth-api-key' => config('travolyo_b2b.auth_api_key'),
            'X-Api-Key' => config('travolyo_b2b.auth_api_key'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
        $this->searchTimeout = max(5, (int) config('travolyo_b2b.search_timeout', config('travolyo_b2b.timeout', 20)));
        $this->connectTimeout = max(2, (int) config('travolyo_b2b.connect_timeout', 5));
        $this->mapper = new TravolyoB2BHotelMapper();
    }

    public function search(SearchHotelDto $dto): array
    {
        if (trim($dto->destination) === '' || trim($dto->checkIn) === '' || trim($dto->checkOut) === '') {
            return [];
        }

        try {
            $response = $this->postJson('/api/v1/hotels/search', [
                'destination' => $dto->destination,
                'check_in' => $dto->checkIn,
                'check_out' => $dto->checkOut,
                'adults' => $dto->adults,
                'children' => $dto->children,
                'rooms' => $dto->rooms,
                'nationality' => 'AE',
                'currency' => $dto->currency,
            ], $this->searchTimeout);

            $payload = $this->responseArray($response);
            $hotels = $this->extractCollection($payload, ['hotels', 'data.hotels']);


            if ($hotels === []) {
                return [];
            }

            $nights = max($dto->nights(), 1);

            return collect($hotels)
                // ->filter(fn (array $hotel) => ($hotel['source'] ?? null) !== 'tasspro_api')
                ->filter(fn (array $hotel) => ($hotel['source'] ?? null) === 'local')
                ->map(fn (array $hotel) => $this->mapper->toOfferDto($hotel, $nights, $dto->currency))
                ->filter(fn (HotelOfferDto $offer) => $this->matchesPriceFilter($offer, $dto))
                ->values()
                ->all();
        } catch (\Throwable $e) {
            Log::warning('Travolyo B2B hotel search failed', [
                'message' => $e->getMessage(),
                'destination' => $dto->destination,
            ]);

            return [];
        }
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
        $hotelCodeCandidates = $this->hotelCodeCandidates($offerId);
        $cityCodeCandidates = $this->cityCodeCandidates($cityCode, $offerId);
        $nights = max((int) Carbon::parse($checkIn)->diffInDays($checkOut), 1);
        $lastRooms = [];

        foreach ($hotelCodeCandidates as $hotelCode) {
            foreach ($cityCodeCandidates as $candidateCityCode) {
                try {
                    $response = $this->postQuery('/api/v1/hotels/rooms', [
                        'hotel_code' => $hotelCode,
                        'city_code' => $candidateCityCode,
                        'check_in' => $checkIn,
                        'check_out' => $checkOut,
                    ], 60);

                    $payload = $this->responseArray($response);
                    $rooms = $this->extractCollection($payload, ['rooms', 'data.rooms']);

                    if ($rooms === []) {
                        continue;
                    }

                    $mappedRooms = collect($rooms)
                        ->map(fn (array $room) => $this->mapper->toRoomOfferDto($room, $nights, $currency, [
                            'offer_id' => $offerId,
                            'hotel_code' => $hotelCode,
                            'city_code' => $candidateCityCode,
                            'adults' => $adults,
                            'children' => $children,
                        ]))
                        ->values()
                        ->all();

                    if ($response->successful()) {
                        return $mappedRooms;
                    }

                    $lastRooms = $mappedRooms;
                } catch (\Throwable $e) {
                    Log::warning('Travolyo B2B hotel rooms lookup failed', [
                        'message' => $e->getMessage(),
                        'offer_id' => $offerId,
                        'hotel_code' => $hotelCode,
                        'city_code' => $candidateCityCode,
                    ]);
                }
            }
        }

        return $lastRooms;
    }

    public function prebook(PrebookHotelDto $dto): HotelOfferDto
    {
        try {
            $roomKeys = $this->mapper->decodeBookingKey($dto->roomId);
        } catch (\InvalidArgumentException $e) {
            throw new HotelException($e->getMessage(), 422);
        }

        $payload = [
            'hotel_code' => (string) ($roomKeys['hotel_code'] ?? $dto->offerId),
            'city_code' => (string) ($roomKeys['city_code'] ?? $this->deriveCityCode($dto->offerId)),
            'check_in' => $dto->checkIn,
            'check_out' => $dto->checkOut,
            'adults' => $dto->adults,
            'children' => $dto->children,
            'rooms' => 1,
            'nationality' => 'AE',
            'search_number' => (string) ($roomKeys['search_number'] ?? ''),
            'token_id' => (string) ($roomKeys['token_id'] ?? ''),
            'rate_key' => (string) ($roomKeys['rate_key'] ?? ''),
            'agreement_code' => (string) ($roomKeys['agreement_code'] ?? ''),
            'agreement_price' => (string) ($roomKeys['agreement_price'] ?? ''),
            'room_type_code' => (string) ($roomKeys['room_type_code'] ?? 'dbl'),
            'meal_basis_code' => (string) ($roomKeys['meal_basis_code'] ?? ''),
        ];

        try {
            $response = $this->postJson('/api/v1/hotels/prebook', $payload, 60);
            $rawPayload = $this->responseArray($response);
            $details = $this->primaryPayload($rawPayload);
            $hasExplicitFailure = array_key_exists('success', $rawPayload) && ! $this->payloadSaysSuccess($rawPayload);

            if (! $response->successful() || $hasExplicitFailure || $details === []) {
                throw new HotelException($this->supplierMessage($rawPayload, 'The selected room is no longer available.'), 422);
            }

            $mergedKeys = array_merge($roomKeys, Arr::only($details, [
                'search_number',
                'token_id',
                'rate_key',
                'agreement_code',
                'agreement_price',
                'room_type_code',
                'room_type_name',
                'meal_basis_code',
                'meal_basis_name',
                'currency',
                'city_code',
                'hotel_code',
                'hotel_name',
                'hotel_city',
                'hotel_country',
                'source',
            ]), [
                'hotel_code' => (string) ($details['hotel_code'] ?? $roomKeys['hotel_code'] ?? $dto->offerId),
                'hotel_name' => (string) ($details['hotel_name'] ?? $roomKeys['hotel_name'] ?? $dto->offerId),
                'hotel_city' => (string) ($details['hotel_city'] ?? $roomKeys['hotel_city'] ?? ''),
                'hotel_country' => (string) ($details['hotel_country'] ?? $roomKeys['hotel_country'] ?? ''),
                'city_code' => (string) ($details['city_code'] ?? $roomKeys['city_code'] ?? $this->deriveCityCode($dto->offerId)),
                'source' => (string) ($details['source'] ?? $roomKeys['source'] ?? ''),
                'agreement_price' => (string) ($details['agreement_price'] ?? $details['total_price'] ?? $roomKeys['agreement_price'] ?? ''),
                'total_price' => (float) ($details['agreement_price'] ?? $details['total_price'] ?? $roomKeys['total_price'] ?? 0),
                'currency' => (string) ($details['currency'] ?? $roomKeys['currency'] ?? $dto->currency),
            ]);

            return $this->mapper->toPrebookOfferDto($details, $mergedKeys, $dto);
        } catch (HotelException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::warning('Travolyo B2B hotel prebook failed', [
                'message' => $e->getMessage(),
                'offer_id' => $dto->offerId,
            ]);

            throw new HotelException('Could not prebook this B2B hotel room. Please try another option.', 422);
        }
    }

    public function book(array $checkoutData, array $guest): array
    {
        try {
            $roomKeys = $this->mapper->decodeBookingKey((string) ($checkoutData['room_id'] ?? ''));
        } catch (\InvalidArgumentException $e) {
            throw new \RuntimeException($e->getMessage());
        }

        $payload = [
            'hotel_code' => (string) ($checkoutData['offer_id'] ?? $roomKeys['hotel_code'] ?? ''),
            'hotel_name' => (string) ($checkoutData['hotel_name'] ?? $roomKeys['hotel_name'] ?? ''),
            'hotel_city' => (string) ($checkoutData['city'] ?? $roomKeys['hotel_city'] ?? ''),
            'agreement_code' => (string) ($roomKeys['agreement_code'] ?? ''),
            'agreement_price' => (string) ($roomKeys['agreement_price'] ?? $checkoutData['total_price'] ?? ''),
            'check_in' => (string) ($checkoutData['check_in'] ?? ''),
            'check_out' => (string) ($checkoutData['check_out'] ?? ''),
            'guest_name' => trim((string) ($guest['first_name'] ?? '') . ' ' . (string) ($guest['last_name'] ?? '')),
            'guest_email' => (string) ($guest['email'] ?? ''),
            'guest_phone' => (string) ($guest['phone'] ?? ''),
            'search_number' => (string) ($roomKeys['search_number'] ?? ''),
            'city_code' => (string) ($checkoutData['city_code'] ?? $roomKeys['city_code'] ?? $this->deriveCityCode((string) ($checkoutData['offer_id'] ?? ''))),
            'token_id' => (string) ($roomKeys['token_id'] ?? ''),
            'rate_key' => (string) ($roomKeys['rate_key'] ?? ''),
            'room_type_code' => (string) ($roomKeys['room_type_code'] ?? 'dbl'),
            'meal_basis_code' => (string) ($roomKeys['meal_basis_code'] ?? ''),
            'adults' => (int) ($checkoutData['adults'] ?? 2),
            'children' => (int) ($checkoutData['children'] ?? 0),
            'rooms' => 1,
            'occupancy' => (int) ($roomKeys['occupancy'] ?? 0),
            'nationality' => (string) ($guest['nationality'] ?? $checkoutData['nationality'] ?? 'AE'),
            'special_requests' => (string) ($checkoutData['special_requests'] ?? ''),
        ];

        $attempt = $this->postBookWithRetry($payload);

        if (! $this->bookSucceeded($attempt)) {
            throw new \RuntimeException($attempt['message'] ?: 'B2B hotel booking failed.');
        }

        $attempt['supplier_status'] = (string) $this->firstFilled($attempt['data'], [
            'status',
            'booking_status',
            'data.status',
            'data.booking_status',
        ], 'confirmed');
        $attempt['supplier_reference'] = (string) $this->firstFilled($attempt['data'], [
            'reference',
            'booking_reference',
            'reference_no',
            'reference_number',
            'confirmation_number',
            'data.reference',
            'data.booking_reference',
            'data.confirmation_number',
        ], $payload['agreement_code']);
        $attempt['supplier_booking_code'] = (string) $this->firstFilled($attempt['data'], [
            'booking_code',
            'booking_id',
            'reservation_id',
            'reservation_code',
            'data.booking_code',
            'data.booking_id',
            'data.reservation_id',
        ], $attempt['supplier_reference']);
        $attempt['confirmed_total_price'] = (float) $this->firstFilled($attempt['data'], [
            'agreement_price',
            'total_price',
            'data.agreement_price',
            'data.total_price',
        ], (float) ($checkoutData['total_price'] ?? 0));

        return $attempt;
    }

    public function getOrder(string $orderId): HotelOrderDto
    {
        $booking = Booking::with('metaItems')
            ->where('code', $orderId)
            ->where('object_model', 'hotel')
            ->firstOrFail();

        $meta = $booking->getJsonMeta('hotel_details');
        $b2bBooking = $booking->getJsonMeta('travolyo_b2b_booking');

        return new HotelOrderDto(
            orderId: $booking->code,
            provider: HotelProviderEnum::TravolyoB2B,
            hotelName: (string) ($meta['hotel_name'] ?? $meta['name'] ?? 'N/A'),
            roomName: (string) ($meta['room_name'] ?? $meta['room_type_name'] ?? $meta['room_type'] ?? 'N/A'),
            checkIn: $booking->start_date?->toDateString() ?? (string) ($meta['check_in'] ?? ''),
            checkOut: $booking->end_date?->toDateString() ?? (string) ($meta['check_out'] ?? ''),
            nights: max((int) ($booking->start_date?->diffInDays($booking->end_date) ?? 0), 1),
            adults: (int) ($meta['adults'] ?? 1),
            children: (int) ($meta['children'] ?? 0),
            totalPrice: (float) ($booking->total ?? $meta['total_price'] ?? 0),
            currency: (string) ($booking->currency ?? $meta['currency'] ?? 'USD'),
            status: (string) ($meta['supplier_status'] ?? $b2bBooking['supplier_status'] ?? $b2bBooking['status'] ?? $booking->status),
            guestFirstName: (string) $booking->first_name,
            guestLastName: (string) $booking->last_name,
            guestEmail: (string) $booking->email,
            guestPhone: (string) $booking->phone,
            specialRequests: $booking->customer_notes,
        );
    }

    public function cancelOrder(string $orderId): bool
    {
        $booking = Booking::with('metaItems')
            ->where('code', $orderId)
            ->where('object_model', 'hotel')
            ->firstOrFail();

        $booking->update(['status' => 'cancelled']);

        $hotelDetails = $booking->getJsonMeta('hotel_details');
        if ($hotelDetails !== []) {
            $hotelDetails['supplier_status'] = 'cancelled';
            $booking->updateMeta('hotel_details', $hotelDetails);
        }

        $b2bBooking = $booking->getJsonMeta('travolyo_b2b_booking');
        if ($b2bBooking !== []) {
            $b2bBooking['supplier_status'] = 'cancelled';
            $booking->updateMeta('travolyo_b2b_booking', $b2bBooking);
        }

        return true;
    }

    protected function postBookWithRetry(array $payload): array
    {
        $attempt = $this->postBookRequest($payload);
        $current = $payload;

        if (! $this->bookSucceeded($attempt) && $this->shouldRetryPassengerNorm($attempt)) {
            $normalized = $this->normalizePassengers($current);
            if ($normalized !== $current) {
                $attempt = $this->postBookRequest($normalized);
                $current = $normalized;
            }
        }

        if (! $this->bookSucceeded($attempt) && $this->shouldRetryFreshPrebook($attempt)) {
            $freshPrebook = $this->callPrebook($current);

            if (! empty($freshPrebook['success'])) {
                $details = $this->primaryPayload($freshPrebook['data']);

                $current['search_number'] = (string) ($details['search_number'] ?? $current['search_number']);
                $current['token_id'] = (string) ($details['token_id'] ?? $current['token_id']);
                $current['rate_key'] = (string) ($details['rate_key'] ?? $current['rate_key']);
                $current['agreement_code'] = (string) ($details['agreement_code'] ?? $current['agreement_code']);
                $current['agreement_price'] = (string) ($details['agreement_price'] ?? $details['total_price'] ?? $current['agreement_price']);
                $current['room_type_code'] = (string) ($details['room_type_code'] ?? $current['room_type_code']);
                $current['meal_basis_code'] = (string) ($details['meal_basis_code'] ?? $current['meal_basis_code']);

                $attempt = $this->postBookRequest($current);
            }
        }

        return $attempt;
    }

    protected function postBookRequest(array $payload): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->connectTimeout($this->connectTimeout)
                ->timeout(600)
                ->retry(3, 2000, null, false)
                ->asJson()
                ->post($this->url('/api/v1/hotels/book'), $payload);

            $data = $this->responseArray($response);

            return [
                'success' => $response->successful() && ($this->payloadSaysSuccess($data) || (! array_key_exists('success', $data) && $data !== [])),
                'message' => $this->supplierMessage($data, ''),
                'status' => $response->status(),
                'data' => $data,
                'payload' => $payload,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'status' => 500,
                'data' => [],
                'payload' => $payload,
            ];
        }
    }

    protected function callPrebook(array $payload): array
    {
        try {
            $response = $this->postJson('/api/v1/hotels/prebook', Arr::only($payload, [
                'hotel_code',
                'city_code',
                'check_in',
                'check_out',
                'adults',
                'children',
                'rooms',
                'nationality',
                'search_number',
                'token_id',
                'rate_key',
                'agreement_code',
                'agreement_price',
                'room_type_code',
                'meal_basis_code',
            ]), 60);

            $data = $this->responseArray($response);
            $hasExplicitFailure = array_key_exists('success', $data) && ! $this->payloadSaysSuccess($data);

            return [
                'success' => $response->successful() && ! $hasExplicitFailure && $this->primaryPayload($data) !== [],
                'data' => $data,
            ];
        } catch (\Throwable) {
            return ['success' => false, 'data' => []];
        }
    }

    protected function bookSucceeded(array $attempt): bool
    {
        return ! empty($attempt['success']);
    }

    protected function shouldRetryFreshPrebook(array $attempt): bool
    {
        $status = $attempt['status'] ?? 0;
        $message = strtolower((string) ($attempt['message'] ?? ''));

        if (! in_array($status, [409, 410, 422], true)) {
            return false;
        }

        return str_contains($message, 'rate no longer available')
            || str_contains($message, 'search again')
            || str_contains($message, 'rate key')
            || str_contains($message, 'token')
            || str_contains($message, 'expired');
    }

    protected function shouldRetryPassengerNorm(array $attempt): bool
    {
        $status = $attempt['status'] ?? 0;
        $message = strtolower((string) ($attempt['message'] ?? ''));

        if (! in_array($status, [409, 422], true)) {
            return false;
        }

        return str_contains($message, 'passengers does not match')
            || (str_contains($message, 'passenger') && str_contains($message, 'room'));
    }

    protected function normalizePassengers(array $payload): array
    {
        $code = strtolower(trim((string) ($payload['room_type_code'] ?? '')));
        $inferred = 0;

        if (str_contains($code, 'sgl') || str_contains($code, 'single')) {
            $inferred = 1;
        } elseif (str_contains($code, 'tpl') || str_contains($code, 'triple')) {
            $inferred = 3;
        } elseif (str_contains($code, 'qd') || str_contains($code, 'quad')) {
            $inferred = 4;
        } elseif (
            str_contains($code, 'dbl') || str_contains($code, 'twn')
            || str_contains($code, 'double') || str_contains($code, 'twin')
        ) {
            $inferred = 2;
        }

        if ($inferred <= 0) {
            return $payload;
        }

        $rooms = max(1, (int) ($payload['rooms'] ?? 1));
        $expected = $inferred * $rooms;
        $adults = max(1, (int) ($payload['adults'] ?? 1));
        $children = max(0, (int) ($payload['children'] ?? 0));
        $current = $adults + $children;

        if ($current < $expected) {
            $adults += ($expected - $current);
        } elseif ($current > $expected) {
            $overflow = $current - $expected;
            $reduceKids = min($children, $overflow);
            $children -= $reduceKids;
            $overflow -= $reduceKids;

            if ($overflow > 0) {
                $adults = max(1, $adults - $overflow);
            }
        }

        $payload['occupancy'] = $inferred;
        $payload['adults'] = max(1, $adults);
        $payload['children'] = max(0, $children);

        return $payload;
    }

    protected function matchesPriceFilter(HotelOfferDto $offer, SearchHotelDto $dto): bool
    {
        if ($dto->priceMin !== null && $offer->convertedLowestPrice < $dto->priceMin) {
            return false;
        }

        if ($dto->priceMax !== null && $offer->convertedLowestPrice > $dto->priceMax) {
            return false;
        }

        return true;
    }

    protected function hotelCodeCandidates(string $offerId): array
    {
        return array_values(array_unique(array_filter([
            $offerId,
            preg_replace('/^[A-Za-z]+/', '', $offerId),
            preg_replace('/\D+/', '', $offerId),
        ])));
    }

    protected function cityCodeCandidates(string $cityCode, string $offerId): array
    {
        $candidates = array_values(array_unique(array_filter([
            $cityCode,
            strtoupper($cityCode),
            $this->deriveCityCode($offerId),
        ])));

        return $candidates !== [] ? $candidates : [''];
    }

    protected function deriveCityCode(string $offerId): string
    {
        if (preg_match('/^([A-Za-z]{3})/', $offerId, $matches)) {
            return strtoupper($matches[1]);
        }

        return '';
    }

    protected function postJson(string $path, array $payload, ?int $timeout = null): Response
    {
        return Http::withHeaders($this->headers)
            ->connectTimeout($this->connectTimeout)
            ->timeout($timeout ?? $this->searchTimeout)
            ->asJson()
            ->post($this->url($path), $payload);
    }

    protected function postQuery(string $path, array $query, ?int $timeout = null): Response
    {
        return Http::withHeaders($this->headers)
            ->connectTimeout($this->connectTimeout)
            ->timeout($timeout ?? $this->searchTimeout)
            ->asJson()
            ->post($this->url($path) . '?' . http_build_query($query));
    }

    protected function responseArray(Response $response): array
    {
        $decoded = $response->json();

        return is_array($decoded) ? $decoded : [];
    }

    protected function extractCollection(array $payload, array $paths): array
    {
        foreach ($paths as $path) {
            $value = data_get($payload, $path);

            if (is_array($value)) {
                return array_values($value);
            }
        }

        return [];
    }

    protected function primaryPayload(array $payload): array
    {
        $data = data_get($payload, 'data');

        if (is_array($data) && $this->isAssociative($data)) {
            return $data;
        }

        return $this->isAssociative($payload) ? $payload : [];
    }

    protected function payloadSaysSuccess(array $payload): bool
    {
        $success = data_get($payload, 'success');

        if (is_bool($success)) {
            return $success;
        }

        if (is_numeric($success)) {
            return (bool) $success;
        }

        if (is_string($success)) {
            return in_array(strtolower($success), ['1', 'true', 'yes', 'ok', 'success'], true);
        }

        return false;
    }

    protected function supplierMessage(array $payload, string $default): string
    {
        return (string) $this->firstFilled($payload, ['message', 'error', 'data.message', 'data.error'], $default);
    }

    protected function firstFilled(array $payload, array $paths, mixed $default = null): mixed
    {
        foreach ($paths as $path) {
            $value = data_get($payload, $path);

            if ($this->hasValue($value)) {
                return $value;
            }
        }

        return $default;
    }

    protected function hasValue(mixed $value): bool
    {
        return ! ($value === null || $value === '' || $value === []);
    }

    protected function isAssociative(array $payload): bool
    {
        if ($payload === []) {
            return false;
        }

        return array_keys($payload) !== range(0, count($payload) - 1);
    }

    protected function url(string $path): string
    {
        return $this->baseUrl . '/' . ltrim($path, '/');
    }
}
