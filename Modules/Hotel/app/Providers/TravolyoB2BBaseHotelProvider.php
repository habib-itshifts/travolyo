<?php

namespace Modules\Hotel\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Modules\Hotel\DTOs\HotelOfferDto;
use Modules\Hotel\DTOs\HotelRoomOfferDto;
use Modules\Hotel\DTOs\PrebookHotelDto;
use Modules\Hotel\DTOs\SearchHotelDto;
use Modules\Hotel\Enums\HotelProviderEnum;

abstract class TravolyoB2BBaseHotelProvider implements HotelProviderInterface
{
    protected string $baseUrl;
    protected array  $headers;
    protected int    $searchTimeout;
    protected int    $connectTimeout;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('travolyo_b2b.base_url'), '/');
        $this->headers = [
            'auth-api-key'  => config('travolyo_b2b.auth_api_key'),
            'X-Api-Key'     => config('travolyo_b2b.auth_api_key'),
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ];
        $this->searchTimeout = max(5, (int) config('travolyo_b2b.search_timeout', config('travolyo_b2b.timeout', 20)));
        $this->connectTimeout = max(2, (int) config('travolyo_b2b.connect_timeout', 5));
    }

    // ── Each subclass declares which source tag it handles ──────
    abstract protected function sourceTag(): string; // 'local' | 'netstorming_api'

    // ── Search ──────────────────────────────────────────────────

    public function search(SearchHotelDto $dto): array
    {
        
        if (empty(trim($dto->destination)) || empty(trim($dto->checkIn)) || empty(trim($dto->checkOut))) {
            return [];
        }

        
        try {
            $response = Http::connectTimeout($this->connectTimeout)
                ->timeout($this->searchTimeout)
                ->withHeaders($this->headers)
                ->post($this->baseUrl . '/api/v1/hotels/search', [
                    'destination' => $dto->destination,
                    'check_in'    => $dto->checkIn,
                    'check_out'   => $dto->checkOut,
                    'adults'      => $dto->adults,
                    'children'    => $dto->children,
                    'rooms'       => $dto->rooms,
                    'nationality' => 'AE',
                    'currency'    => $dto->currency,
                ]);

            if (! $response->successful()) {
                return [];
            }

            $hotels = $response->json('hotels') ?? $response->json('data.hotels') ?? [];
            if (! is_array($hotels)) {
                return [];
            }

            $nights   = $dto->nights();
            $provider = $this->providerEnum();

            return collect($hotels)
                ->filter(fn (array $h) => ($h['source'] ?? '') === $this->sourceTag())
                ->map(fn (array $h) => $this->mapHotelToDto($h, $nights, $provider, $dto->currency))
                ->values()
                ->all();

        } catch (\Throwable $e) {
            Log::warning('B2B hotel search failed', [
                'provider' => $this->sourceTag(),
                'destination' => $dto->destination,
                'message' => $e->getMessage(),
            ]);
            return [];
        }
    }

    // ── Get Rooms ────────────────────────────────────────────────

    public function getRooms(
        string $offerId,
        string $cityCode,
        string $checkIn,
        string $checkOut,
        int    $adults,
        int    $children,
    ): array {
        $hotelCodeCandidates = array_values(array_unique(array_filter([
            $offerId,
            preg_replace('/^[A-Za-z]+/', '', $offerId),  // DXB9890252 → 9890252
            preg_replace('/\D+/', '', $offerId),          // digits only
        ])));

        $cityCodeCandidates = array_values(array_unique(array_filter([
            $cityCode,
            strtoupper($cityCode),
            preg_match('/^([A-Za-z]{3})/', $offerId, $m) ? strtoupper($m[1]) : null,
        ])));

        if (empty($cityCodeCandidates)) {
            $cityCodeCandidates = [''];
        }

        $lastRooms = [];

        foreach ($hotelCodeCandidates as $hotelCode) {
            foreach ($cityCodeCandidates as $cc) {
                try {
                    $response = Http::timeout(60)
                        ->withHeaders($this->headers)
                        ->post($this->baseUrl . '/api/v1/hotels/rooms?' . http_build_query([
                            'hotel_code' => $hotelCode,
                            'city_code'  => $cc,
                            'check_in'   => $checkIn,
                            'check_out'  => $checkOut,
                        ]));

                    $data  = $response->json();
                    $rooms = $data['rooms'] ?? [];

                    if ($response->successful() && ! empty($data['success']) && ! empty($rooms)) {
                        $nights = (int) \Carbon\Carbon::parse($checkIn)->diffInDays($checkOut);
                        return $this->mapRooms($rooms, $adults, $children, $nights);
                    }

                    if (! empty($rooms)) {
                        $nights = (int) \Carbon\Carbon::parse($checkIn)->diffInDays($checkOut);
                        $lastRooms = $this->mapRooms($rooms, $adults, $children, $nights);
                    }
                } catch (\Throwable) {
                    // continue to next candidate
                }
            }
        }

        return $lastRooms;
    }

    // ── Prebook ──────────────────────────────────────────────────

    public function prebook(PrebookHotelDto $dto): HotelOfferDto
    {
        // room_id is base64-encoded JSON of B2B booking keys
        $roomKeys = $this->decodeRoomId($dto->roomId);

        $payload = array_merge($roomKeys, [
            'hotel_code'  => $dto->offerId,
            'check_in'    => $dto->checkIn,
            'check_out'   => $dto->checkOut,
            'adults'      => $dto->adults,
            'children'    => $dto->children,
            'rooms'       => 1,
            'nationality' => 'AE',
        ]);

        try {
            $response = Http::timeout(60)
                ->withHeaders($this->headers)
                ->post($this->baseUrl . '/api/v1/hotels/prebook', $payload);

            $data = $response->json();

            if (! is_array($data)) {
                $data = [];
            }

            // Merge updated keys back into roomKeys for the final hotel offer
            $updatedKeys = array_merge($roomKeys, [
                'search_number'   => (string) ($data['search_number']   ?? $roomKeys['search_number']   ?? ''),
                'token_id'        => (string) ($data['token_id']        ?? $roomKeys['token_id']        ?? ''),
                'rate_key'        => (string) ($data['rate_key']        ?? $roomKeys['rate_key']        ?? ''),
                'agreement_code'  => (string) ($data['agreement_code']  ?? $roomKeys['agreement_code']  ?? ''),
                'agreement_price' => (string) ($data['agreement_price'] ?? $data['total_price'] ?? $roomKeys['agreement_price'] ?? ''),
                'room_type_code'  => (string) ($data['room_type_code']  ?? $roomKeys['room_type_code']  ?? 'dbl'),
                'meal_basis_code' => (string) ($data['meal_basis_code'] ?? $roomKeys['meal_basis_code'] ?? ''),
            ]);

            $nights     = (int) \Carbon\Carbon::parse($dto->checkIn)->diffInDays($dto->checkOut);
            $totalPrice = (float) ($data['agreement_price'] ?? $data['total_price'] ?? $roomKeys['total_price'] ?? 0);
            $basePrice  = $nights > 0 && $totalPrice > 0 ? round($totalPrice / $nights, 2) : $totalPrice;
            $currency   = $data['currency'] ?? $roomKeys['currency'] ?? $dto->currency;

            // Build updated room DTO with new prebook keys encoded in roomId
            $updatedRoomId = $this->encodeRoomId(array_merge($updatedKeys, [
                'total_price' => $totalPrice,
                'currency'    => $currency,
            ]));

            $room = new HotelRoomOfferDto(
                roomId:           $updatedRoomId,
                name:             $data['room_type_name'] ?? $roomKeys['room_type_name'] ?? 'Room',
                roomType:         $updatedKeys['room_type_code'],
                bedConfiguration: [],
                maxAdults:        $dto->adults,
                maxChildren:      $dto->children,
                basePrice:        $basePrice,
                totalPrice:       $totalPrice,
                nights:           $nights,
                currency:         $currency,
                isAvailable:      true,
                amenityNames:     [],
            );

            return new HotelOfferDto(
                offerId:          $dto->offerId,
                provider:         $this->providerEnum(),
                name:             $data['hotel_name'] ?? $dto->offerId,
                starRating:       0,
                city:             $data['hotel_city'] ?? '',
                country:          '',
                address:          '',
                description:      null,
                shortDescription: null,
                checkInTime:      null,
                checkOutTime:     null,
                latitude:         null,
                longitude:        null,
                images:           [],
                amenityNames:     [],
                serviceNames:     [],
                lowestPrice:      $totalPrice,
                currency:         $currency,
                rooms:            [$room],
            );

        } catch (\Throwable $e) {
            throw new \RuntimeException('B2B hotel prebook failed: ' . $e->getMessage());
        }
    }

    // ── Book (called after payment confirmation) ─────────────────

    public function book(string $bookingCode, array $guestData): array
    {
        $roomKeys = $this->decodeRoomId($guestData['room_id'] ?? '');

        $payload = [
            'hotel_code'       => $guestData['hotel_code'] ?? '',
            'hotel_name'       => $guestData['hotel_name'] ?? '',
            'hotel_city'       => $guestData['city'] ?? '',
            'agreement_code'   => $roomKeys['agreement_code']  ?? '',
            'agreement_price'  => $roomKeys['agreement_price'] ?? '',
            'check_in'         => $guestData['check_in']  ?? '',
            'check_out'        => $guestData['check_out'] ?? '',
            'guest_name'       => trim(($guestData['first_name'] ?? '') . ' ' . ($guestData['last_name'] ?? '')),
            'guest_email'      => $guestData['email'] ?? '',
            'guest_phone'      => $guestData['phone'] ?? '',
            'search_number'    => $roomKeys['search_number']   ?? '',
            'city_code'        => $guestData['city_code']      ?? '',
            'token_id'         => $roomKeys['token_id']        ?? '',
            'rate_key'         => $roomKeys['rate_key']        ?? '',
            'room_type_code'   => $roomKeys['room_type_code']  ?? 'dbl',
            'meal_basis_code'  => $roomKeys['meal_basis_code'] ?? '',
            'adults'           => (int) ($guestData['adults']   ?? 2),
            'children'         => (int) ($guestData['children'] ?? 0),
            'rooms'            => 1,
            'occupancy'        => (int) ($roomKeys['occupancy'] ?? 0),
            'nationality'      => $guestData['nationality'] ?? 'AE',
            'special_requests' => $guestData['special_requests'] ?? '',
        ];

        return $this->postBookWithRetry($payload);
    }

    // ── getOrder / cancelOrder ───────────────────────────────────

    public function getOrder(string $orderId): \Modules\Hotel\DTOs\HotelOrderDto
    {
        throw new \RuntimeException('B2B hotel getOrder not yet implemented.');
    }

    public function cancelOrder(string $orderId): bool
    {
        throw new \RuntimeException('B2B hotel cancelOrder not yet implemented.');
    }

    // ── Helpers ──────────────────────────────────────────────────

    protected function providerEnum(): HotelProviderEnum
    {
        return match ($this->sourceTag()) {
            'local'           => HotelProviderEnum::TravolyoB2BLocal,
            'netstorming_api' => HotelProviderEnum::TravolyoB2BNetStreaming,
            'tasspro_api'     => HotelProviderEnum::TravolyoB2BTassPro,
            default           => HotelProviderEnum::TravolyoB2BLocal,
        };
    }

    protected function mapHotelToDto(array $h, int $nights, HotelProviderEnum $provider, string $currency): HotelOfferDto
    {
        $lowestPrice = (float) ($h['min_price'] ?? 0);

        return new HotelOfferDto(
            offerId:          (string) ($h['hotel_code'] ?? $h['id'] ?? uniqid()),
            provider:         $provider,
            name:             (string) ($h['name'] ?? ''),
            starRating:       (int) ($h['star_rating'] ?? 0),
            city:             (string) ($h['city'] ?? ''),
            country:          (string) ($h['country'] ?? ''),
            address:          (string) ($h['address'] ?? ''),
            description:      $h['description'] ?? null,
            shortDescription: null,
            checkInTime:      null,
            checkOutTime:     null,
            latitude:         isset($h['latitude'])  ? (float) $h['latitude']  : null,
            longitude:        isset($h['longitude']) ? (float) $h['longitude'] : null,
            images:           isset($h['image_url']) ? [$h['image_url']] : ($h['images'] ?? []),
            amenityNames:     $this->parseFacilities($h),
            serviceNames:     [],
            lowestPrice:      $lowestPrice,
            currency:         (string) ($h['currency'] ?? $currency),
            rooms:            [], // rooms fetched separately via /api/v1/hotels/rooms
            badge:            $lowestPrice > 0 ? null : null,
        );
    }

    protected function mapRooms(array $rooms, int $adults, int $children, int $nights): array
    {
        $result = [];

        foreach ($rooms as $r) {
            // Encode all B2B booking keys into the roomId so prebook can retrieve them later
            $roomId = $this->encodeRoomId([
                'agreement_code'  => (string) ($r['agreement_code']  ?? ''),
                'token_id'        => (string) ($r['token_id']        ?? ''),
                'rate_key'        => (string) ($r['rate_key']        ?? ''),
                'search_number'   => (string) ($r['search_number']   ?? ''),
                'agreement_price' => (string) ($r['agreement_price'] ?? $r['total_price'] ?? ''),
                'room_type_code'  => (string) ($r['room_type_code']  ?? 'dbl'),
                'room_type_name'  => (string) ($r['room_type_name']  ?? 'Room'),
                'meal_basis_code' => (string) ($r['meal_basis_code'] ?? ''),
                'meal_basis_name' => (string) ($r['meal_basis_name'] ?? ''),
                'occupancy'       => (int)    ($r['occupancy']       ?? $adults),
                'total_price'     => (float)  ($r['total_price']     ?? 0),
                'currency'        => (string) ($r['currency']        ?? 'USD'),
            ]);

            $totalPrice  = (float) ($r['total_price']    ?? 0);
            $basePrice   = (float) ($r['price_per_night'] ?? ($nights > 0 && $totalPrice > 0 ? round($totalPrice / $nights, 2) : $totalPrice));
            $currency    = (string) ($r['currency'] ?? 'USD');

            $result[] = new HotelRoomOfferDto(
                roomId:           $roomId,
                name:             (string) ($r['room_type_name'] ?? 'Room'),
                roomType:         (string) ($r['room_type_code'] ?? ''),
                bedConfiguration: [],
                maxAdults:        (int) ($r['adults']    ?? $r['occupancy'] ?? $adults),
                maxChildren:      (int) ($r['children']  ?? 0),
                basePrice:        $basePrice,
                totalPrice:       $totalPrice,
                nights:           $nights,
                currency:         $currency,
                isAvailable:      true,
                amenityNames:     array_filter([
                    $r['meal_basis_name'] ?? null,
                ]),
            );
        }

        return $result;
    }

    protected function parseFacilities(array $h): array
    {
        $raw = $h['facilities'] ?? [];

        if (is_string($raw)) {
            return array_map('trim', explode(',', $raw));
        }

        if (is_array($raw)) {
            $flat = [];
            foreach ($raw as $item) {
                if (is_string($item)) {
                    foreach (explode(',', $item) as $part) {
                        $part = trim($part);
                        if ($part !== '') {
                            $flat[] = $part;
                        }
                    }
                }
            }
            return $flat;
        }

        return [];
    }

    protected function encodeRoomId(array $keys): string
    {
        return base64_encode(json_encode($keys));
    }

    protected function decodeRoomId(string $roomId): array
    {
        try {
            $decoded = base64_decode($roomId, strict: true);
            if ($decoded === false) {
                // Maybe it's plain JSON
                $decoded = $roomId;
            }
            return json_decode($decoded, associative: true) ?? [];
        } catch (\Throwable) {
            return [];
        }
    }

    // ── Book with retry logic (ported from old HotelSearchService) ──

    protected function postBookWithRetry(array $payload): array
    {
        $attempt = $this->postBookRequest($payload);
        $current = $payload;

        // Retry 1: normalize passengers if occupancy mismatch
        if (! $this->bookSucceeded($attempt)) {
            if ($this->shouldRetryPassengerNorm($attempt)) {
                $normalized = $this->normalizePassengers($current);
                if ($normalized !== $current) {
                    $attempt = $this->postBookRequest($normalized);
                    $current = $normalized;
                }
            }
        }

        // Retry 2: fresh prebook if rate expired
        if (! $this->bookSucceeded($attempt)) {
            if ($this->shouldRetryFreshPrebook($attempt)) {
                $freshPrebook = $this->callPrebook($current);
                if (! empty($freshPrebook['success'])) {
                    $fd = $freshPrebook['data'] ?? [];
                    $current['search_number']   = (string) ($fd['search_number']   ?? $current['search_number']);
                    $current['token_id']        = (string) ($fd['token_id']        ?? $current['token_id']);
                    $current['rate_key']        = (string) ($fd['rate_key']        ?? $current['rate_key']);
                    $current['agreement_code']  = (string) ($fd['agreement_code']  ?? $current['agreement_code']);
                    $current['agreement_price'] = (string) ($fd['agreement_price'] ?? $fd['total_price'] ?? $current['agreement_price']);
                    $current['room_type_code']  = (string) ($fd['room_type_code']  ?? $current['room_type_code']);
                    $current['meal_basis_code'] = (string) ($fd['meal_basis_code'] ?? $current['meal_basis_code']);

                    $attempt = $this->postBookRequest($current);
                }
            }
        }

        return $attempt;
    }

    protected function postBookRequest(array $payload): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->timeout(600)
                ->retry(3, 2000, null, false)
                ->post($this->baseUrl . '/api/v1/hotels/book', $payload);

            $data = $response->json();
            if (! is_array($data)) {
                $data = [];
            }

            return [
                'success'  => $response->successful() && ! empty($data['success']),
                'message'  => $data['message'] ?? $data['error'] ?? '',
                'status'   => $response->status(),
                'data'     => $data,
                'payload'  => $payload,
                'response' => $response,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'status'  => 500,
                'data'    => [],
                'payload' => $payload,
            ];
        }
    }

    protected function callPrebook(array $payload): array
    {
        try {
            $response = Http::timeout(60)
                ->withHeaders($this->headers)
                ->post($this->baseUrl . '/api/v1/hotels/prebook', $payload);

            $data = $response->json();
            if (! is_array($data)) {
                $data = [];
            }

            return [
                'success' => $response->successful() && ! empty($data['success']),
                'data'    => $data,
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
        $status  = $attempt['status'] ?? 0;
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
        $status  = $attempt['status'] ?? 0;
        $message = strtolower((string) ($attempt['message'] ?? ''));

        if (! in_array($status, [409, 422], true)) {
            return false;
        }

        return str_contains($message, 'passengers does not match')
            || (str_contains($message, 'passenger') && str_contains($message, 'room'));
    }

    protected function normalizePassengers(array $payload): array
    {
        $code    = strtolower(trim($payload['room_type_code'] ?? ''));
        $inferred = 0;

        if (str_contains($code, 'sgl') || str_contains($code, 'single'))              $inferred = 1;
        elseif (str_contains($code, 'tpl') || str_contains($code, 'triple'))          $inferred = 3;
        elseif (str_contains($code, 'qd')  || str_contains($code, 'quad'))            $inferred = 4;
        elseif (str_contains($code, 'dbl') || str_contains($code, 'twn')
             || str_contains($code, 'double') || str_contains($code, 'twin'))         $inferred = 2;

        if ($inferred <= 0) {
            return $payload;
        }

        $rooms    = max(1, (int) ($payload['rooms'] ?? 1));
        $expected = $inferred * $rooms;
        $adults   = max(1, (int) ($payload['adults'] ?? 1));
        $children = max(0, (int) ($payload['children'] ?? 0));
        $current  = $adults + $children;

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
        $payload['adults']    = max(1, $adults);
        $payload['children']  = max(0, $children);

        return $payload;
    }
}
