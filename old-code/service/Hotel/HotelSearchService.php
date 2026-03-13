<?php

namespace App\Http\Services\Frontend\Hotel;

use App\Http\Dtos\Frontend\Hotel\HotelDTO;
use App\Http\Dtos\Frontend\Hotel\HotelListDto;
use App\Http\Dtos\Frontend\Hotel\HotelSearchRequestDto as FrontendHotelSearchRequestDto;
use App\Http\Dtos\Frontend\Hotel\HotelSearchResponseDto as FrontendHotelSearchResponseDto;
use App\Http\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Modules\Hotel\Dtos\HotelSearchRequestDto as ModuleHotelSearchRequestDto;
use Modules\Hotel\Dtos\HotelSearchResponseDto as ModuleHotelSearchResponseDto;

class HotelSearchService
{
    private const PER_PAGE = 12;
    protected array $headers;
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = (string) config('tavolyo_b2b.base_url');
        $this->headers = [
            'auth-api-key' => config('tavolyo_b2b.auth_api_key'),
            'X-Api-Key' => config('tavolyo_b2b.auth_api_key'),
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Main entry point called by the controller.
     * Merges all sources and returns paginated DTOs.
     */
    public function getListings(Request $request): LengthAwarePaginator
    {
        $hotels = collect();

        // ── Source 1: Local database ──────────────────────────────
        $hotels = $hotels->merge($this->getLocalHotels($request));

        // ── Source 2: Travolyo B2B API ────────────────────────────
        // Comment out the line below to disable this source
        $hotels = $hotels->merge($this->getB2BHotels($request));

        return $this->paginate($hotels);
    }

    // ─────────────────────────────────────────────────────────────
    // PRIVATE: Local DB
    // ─────────────────────────────────────────────────────────────

    private function getLocalHotels(Request $request): Collection
    {
        $query = Hotel::published()->featuredFirst();

        $locationId = (int) $request->input('location_id', 0);
        if ($locationId > 0) {
            $query->where('location_id', $locationId);
        }

        $locationInput = trim((string) $request->input('location', ''));
        $locationText = trim((string) $request->input('location_text', ''));
        $locationNeedle = $locationText;

        // Airport/city code (e.g. DXB) should not be used as a text LIKE filter.
        if ($locationNeedle === '' && $locationInput !== '' && !preg_match('/^[A-Z]{2,6}$/', $locationInput)) {
            $locationNeedle = $locationInput;
        }

        if ($locationId <= 0 && $locationNeedle !== '') {
            $query->where(function ($builder) use ($locationNeedle) {
                $builder->where('title', 'like', '%' . $locationNeedle . '%')
                    ->orWhere('address', 'like', '%' . $locationNeedle . '%');
            });
        }

        return $query
            ->get()
            ->map(fn(Hotel $hotel) => new HotelDTO(
                id:             'local_' . $hotel->id,
                source:         'local_db',
                name:           $hotel->title ?? '',
                address:        $hotel->address ?? '',
                image_url:      $hotel->image_url,
                price:          (float) $hotel->current_price,
                original_price: $hotel->original_price ? (float) $hotel->original_price : null,
                star_rate:      (int) ($hotel->star_rate ?? 0),
                slug:           $hotel->slug,
            ));
    }

    // ─────────────────────────────────────────────────────────────
    // PRIVATE: Travolyo B2B API
    // ─────────────────────────────────────────────────────────────

    private function getB2BHotels(Request $request): Collection
    {
        $dto = FrontendHotelSearchRequestDto::fromRequest($request);

        // Skip API call if required search params are missing
        if (empty(trim($dto->destination)) || empty(trim($dto->check_in)) || empty(trim($dto->check_out))) {
            return collect();
        }

        $response = Http::timeout(120)
            ->withHeaders($this->headers)
            ->post(
                rtrim($this->baseUrl, '/') . '/api/v1/hotels/search',
                $dto->toArray()
            );

        if (! $response->successful()) {
            return collect();
        }

        $responseDto = FrontendHotelSearchResponseDto::fromArray((array) $response->json());

        return collect($responseDto->hotels)
            ->map(fn(HotelListDto $hotel) => new HotelDTO(
                id:             'b2b_' . $hotel->code,
                source:         'travolyob2b',
                name:           $hotel->name,
                address:        $hotel->fullAddress,
                image_url:      $hotel->thumbnailUrl ?: asset('images/placeholder-hotel.jpg'),
                price:          $hotel->pricePerNight,
                original_price: null,
                star_rate:      $hotel->starRating,
                slug:           null,
            ));
    }

    // ─────────────────────────────────────────────────────────────
    // PRIVATE: In-memory pagination
    // ─────────────────────────────────────────────────────────────

    private function paginate(Collection $items): LengthAwarePaginator
    {
        $page = (int) request()->input('page', 1);

        return new LengthAwarePaginator(
            items:       $items->forPage($page, self::PER_PAGE),
            total:       $items->count(),
            perPage:     self::PER_PAGE,
            currentPage: $page,
            options:     [
                'path'  => request()->url(),
                'query' => request()->query(),
            ]
        );
    }


    public function getCountryList(): Collection
    {
        $response = Http::timeout(120)
            ->withHeaders($this->headers)
            ->get(rtrim($this->baseUrl, '/') . '/api/v1/hotels/destinations');

        $destinations = collect($this->extractDestinations($response))
            ->merge($this->getHardcodedDestinations());

        return $destinations
            ->map(function ($item) {
                return [
                    'id' => $item['country_code'] ?? '',
                    'title' => trim($item['country'] ?? ''),
                ];
            })
            ->filter(fn($item) => !empty($item['id']) && !empty($item['title']))
            ->unique('id')
            ->values();
    }

    public function getCityList(?string $countryCode = null): Collection
    {
        $response = Http::timeout(120)
            ->withHeaders($this->headers)
            ->get(rtrim($this->baseUrl, '/') . '/api/v1/hotels/destinations');

        $destinations = collect($this->extractDestinations($response))
            ->merge($this->getHardcodedDestinations());

        if (!empty($countryCode)) {
            $destinations = $destinations->filter(function ($item) use ($countryCode) {
                return strtoupper((string)($item['country_code'] ?? '')) === strtoupper($countryCode);
            });
        }

        return $destinations
            ->map(function ($item) {
                return [
                    'id' => $item['city_code'] ?? '',
                    'title' => trim($item['city'] ?? ''),
                ];
            })
            ->filter(fn($item) => !empty($item['id']) && !empty($item['title']))
            ->unique('id')
            ->values();
    }

    protected function extractDestinations($response): array
    {
        if (!$response->successful()) {
            return [];
        }

        $destinations = $response->json('destinations');
        if (!is_array($destinations)) {
            $destinations = $response->json('data.destinations', []);
        }

        return is_array($destinations) ? $destinations : [];
    }

    protected function getHardcodedDestinations(): array
    {
        return [
            [
                "city" => "Dubai",
                "country" => "United Arab Emirates",
                "country_code" => "AE",
                "city_code" => "DXB",
                "source" => "netstorming",
            ]
        ];
    }

    public function getHotelListFormB2B(Request $request)
    {
        $dto = ModuleHotelSearchRequestDto::fromRequest($request);

        // Guard: listing page can open without search params (e.g., redirects after payment failure).
        // In that case return an empty payload instead of calling provider with invalid/null values.
        if (empty(trim($dto->destination)) || empty(trim($dto->check_in)) || empty(trim($dto->check_out))) {
            return response()->json([
                'success' => false,
                'total_count' => 0,
                'check_in_date' => '',
                'check_out_date' => '',
                'total_nights' => 0,
                'destination_name' => '',
                'hotels' => [],
                'message' => 'Search parameters are missing',
            ]);
        }

        $response = Http::withHeaders($this->headers)
            ->post(rtrim($this->baseUrl, '/') . '/api/v1/hotels/search', $dto->toArray());

        $responseDto = ModuleHotelSearchResponseDto::fromArray((array) $response->json());

        return response()->json($responseDto->toArray());
    }

    public function getHotelRoomsFormB2B(Request $request)
    {
        $request->validate([
            'hotel_code' => 'required|string',
            'check_in' => 'required|date_format:Y-m-d',
            'check_out' => 'required|date_format:Y-m-d',
            'city_code' => 'nullable|string',
        ]);

        $baseUrl = rtrim($this->baseUrl, '/') . '/api/v1/hotels/rooms';

        try {
            $hotelCodeInput = trim((string)$request->input('hotel_code'));
            $cityCodeInput = trim((string)$request->input('city_code'));
            $checkIn = $request->input('check_in');
            $checkOut = $request->input('check_out');

            // Try multiple compatible code variants to handle provider-specific code formats.
            $hotelCodeCandidates = array_values(array_unique(array_filter([
                $hotelCodeInput,
                preg_replace('/^[A-Za-z]+/', '', $hotelCodeInput), // DXB9890252 -> 9890252
                preg_replace('/\D+/', '', $hotelCodeInput),        // keep digits only
            ])));

            $cityCodeCandidates = array_values(array_unique(array_filter([
                $cityCodeInput,
                strtoupper($cityCodeInput),
                preg_match('/^([A-Za-z]{3})/', $hotelCodeInput, $m) ? strtoupper($m[1]) : null, // DXB...
            ])));

            if (empty($cityCodeCandidates)) {
                $cityCodeCandidates = [''];
            }

            $lastResponse = null;
            $lastPayload = null;

            foreach ($hotelCodeCandidates as $hotelCode) {
                foreach ($cityCodeCandidates as $cityCode) {
                    $response = Http::withHeaders($this->headers)->post($baseUrl . '?' . http_build_query([
                        'hotel_code' => $hotelCode,
                        'city_code' => $cityCode,
                        'check_in' => $checkIn,
                        'check_out' => $checkOut,
                    ]));

                    $payload = $response->json();
                    $lastResponse = $response;
                    $lastPayload = $payload;

                    if ($response->successful() && !empty($payload['success']) && !empty($payload['rooms'])) {
                        return response()->json($payload);
                    }
                }
            }

            if ($lastResponse && $lastResponse->successful() && is_array($lastPayload)) {
                // Return last successful payload even if rooms are empty.
                return response()->json($lastPayload);
            }

            return response()->json([
                'success' => false,
                'message' => $lastPayload['message'] ?? 'Unable to fetch rooms',
                'status' => $lastResponse ? $lastResponse->status() : 500,
            ], $lastResponse ? $lastResponse->status() : 500);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function getHotelPrebookFromB2B(array $payload): array
    {
        $basePayload = [
            'hotel_code' => trim((string) ($payload['hotel_code'] ?? '')),
            'agreement_code' => trim((string) ($payload['agreement_code'] ?? '')),
            'check_in' => trim((string) ($payload['check_in'] ?? '')),
            'check_out' => trim((string) ($payload['check_out'] ?? '')),
            'search_number' => trim((string) ($payload['search_number'] ?? '')),
            'token_id' => trim((string) ($payload['token_id'] ?? '')),
            'rate_key' => trim((string) ($payload['rate_key'] ?? '')),
            'agreement_price' => $payload['agreement_price'] ?? null,
            'room_type_code' => trim((string) ($payload['room_type_code'] ?? 'dbl')),
            'rooms' => (int) ($payload['rooms'] ?? 1),
            'occupancy' => (int) ($payload['occupancy'] ?? 0),
            'adults' => (int) ($payload['adults'] ?? 2),
            'children' => (int) ($payload['children'] ?? 0),
            'nationality' => trim((string) ($payload['nationality'] ?? 'AE')),
        ];

        try {
            $response = Http::withHeaders($this->headers)
                ->post(rtrim($this->baseUrl, '/') . '/api/v1/hotels/prebook', $basePayload);

            $data = $response->json();
            if (!is_array($data)) {
                $data = [];
            }

            if (!$response->successful() || empty($data['success'])) {
                return [
                    'success' => false,
                    'message' => $data['message'] ?? 'Unable to prebook room',
                    'status' => $response->status(),
                    'data' => $data,
                ];
            }

            return [
                'success' => true,
                'message' => $data['message'] ?? 'Prebook successful',
                'status' => $response->status(),
                'data' => $data,
            ];
        } catch (\Throwable $th) {
            return [
                'success' => false,
                'message' => $th->getMessage(),
                'status' => 500,
                'data' => [],
            ];
        }
    }

    public function bookHotelFromB2B(array $payload): array
    {
        $basePayload = [
            'hotel_code' => trim((string) ($payload['hotel_code'] ?? '')),
            'agreement_code' => trim((string) ($payload['agreement_code'] ?? '')),
            'agreement_price' => (string) ($payload['agreement_price'] ?? ''),
            'check_in' => trim((string) ($payload['check_in'] ?? '')),
            'check_out' => trim((string) ($payload['check_out'] ?? '')),
            'guest_name' => trim((string) ($payload['guest_name'] ?? '')),
            'guest_email' => trim((string) ($payload['guest_email'] ?? '')),
            'guest_phone' => trim((string) ($payload['guest_phone'] ?? '')),
            'search_number' => trim((string) ($payload['search_number'] ?? '')),
            'city_code' => trim((string) ($payload['city_code'] ?? '')),
            'token_id' => trim((string) ($payload['token_id'] ?? '')),
            'rate_key' => trim((string) ($payload['rate_key'] ?? '')),
            'hotel_name' => trim((string) ($payload['hotel_name'] ?? '')),
            'hotel_city' => trim((string) ($payload['hotel_city'] ?? '')),
            'room_type_code' => trim((string) ($payload['room_type_code'] ?? 'dbl')),
            'meal_basis_code' => trim((string) ($payload['meal_basis_code'] ?? '')),
            'adults' => (int) ($payload['adults'] ?? 2),
            'children' => (int) ($payload['children'] ?? 0),
            'rooms' => (int) ($payload['rooms'] ?? 1),
            'occupancy' => (int) ($payload['occupancy'] ?? 0),
            'nationality' => trim((string) ($payload['nationality'] ?? 'AE')),
            'special_requests' => trim((string) ($payload['special_requests'] ?? '')),
        ];

        try {
            $bookAttempt = $this->postBookRequest($basePayload);
            $response = $bookAttempt['response'];
            $data = $bookAttempt['data'];
            $currentPayload = $basePayload;

            // Provider can reject occupancy/pax mismatch for selected room type.
            // Retry once with normalized pax payload compatible with room capacity.
            if (!$response->successful() || empty($data['success'])) {
                if ($this->shouldRetryWithPassengerNormalization($response->status(), $data)) {
                    $normalizedPayload = $this->normalizePassengerPayloadForRoomCapacity($currentPayload);
                    if ($normalizedPayload !== $currentPayload) {
                        $bookRetry = $this->postBookRequest($normalizedPayload);
                        $response = $bookRetry['response'];
                        $data = $bookRetry['data'];
                        $currentPayload = $normalizedPayload;
                    }
                }
            }

            // Provider rates can expire between prebook and post-payment booking.
            // Retry once with a fresh prebook snapshot to get latest search/token/rate keys.
            if (!$response->successful() || empty($data['success'])) {
                if ($this->shouldRetryWithFreshPrebook($response->status(), $data)) {
                    $prebookPayload = [
                        'hotel_code' => $currentPayload['hotel_code'],
                        'agreement_code' => $currentPayload['agreement_code'],
                        'check_in' => $currentPayload['check_in'],
                        'check_out' => $currentPayload['check_out'],
                        'search_number' => $currentPayload['search_number'],
                        'token_id' => $currentPayload['token_id'],
                        'rate_key' => $currentPayload['rate_key'],
                        'agreement_price' => $currentPayload['agreement_price'],
                        'room_type_code' => $currentPayload['room_type_code'],
                        'rooms' => $currentPayload['rooms'],
                        'occupancy' => $currentPayload['occupancy'],
                        'adults' => $currentPayload['adults'],
                        'children' => $currentPayload['children'],
                        'nationality' => $currentPayload['nationality'],
                    ];

                    $freshPrebook = $this->getHotelPrebookFromB2B($prebookPayload);
                    $freshData = (array) ($freshPrebook['data'] ?? []);

                    if (!empty($freshPrebook['success'])) {
                        $currentPayload['search_number'] = (string) ($freshData['search_number'] ?? $currentPayload['search_number']);
                        $currentPayload['token_id'] = (string) ($freshData['token_id'] ?? $currentPayload['token_id']);
                        $currentPayload['rate_key'] = (string) ($freshData['rate_key'] ?? $currentPayload['rate_key']);
                        $currentPayload['agreement_code'] = (string) ($freshData['agreement_code'] ?? $currentPayload['agreement_code']);
                        $currentPayload['agreement_price'] = (string) ($freshData['agreement_price'] ?? $freshData['total_price'] ?? $currentPayload['agreement_price']);
                        $currentPayload['room_type_code'] = (string) ($freshData['room_type_code'] ?? $currentPayload['room_type_code']);
                        $currentPayload['meal_basis_code'] = (string) ($freshData['meal_basis_code'] ?? $currentPayload['meal_basis_code']);

                        $bookRetry = $this->postBookRequest($currentPayload);
                        $response = $bookRetry['response'];
                        $data = $bookRetry['data'];
                    }
                }

                if (!$response->successful() || empty($data['success'])) {
                    $errorMessage = $data['message'] ?? $data['error'] ?? ('Booking API failed with status ' . $response->status());
                    return [
                        'success' => false,
                        'message' => $errorMessage,
                        'status' => $response->status(),
                        'data' => $data,
                        'request_payload' => $currentPayload,
                    ];
                }
            }

            return [
                'success' => true,
                'message' => $data['message'] ?? 'Hotel booked successfully',
                'status' => $response->status(),
                'data' => $data,
                'request_payload' => $currentPayload,
            ];
        } catch (\Throwable $th) {
            return [
                'success' => false,
                'message' => $th->getMessage(),
                'status' => 500,
                'data' => [],
                'request_payload' => $basePayload,
            ];
        }
    }

    protected function postBookRequest(array $payload): array
    {
        $response = Http::withHeaders($this->headers)
            ->timeout(600) // 10 minutes
            ->retry(3, 2000, null, false) // 3 attempts, 2 sec wait, don't throw on 4xx/5xx
            ->post(rtrim($this->baseUrl, '/') . '/api/v1/hotels/book', $payload);

        $data = $response->json();
        if (!is_array($data)) {
            $data = [];
        }

        return [
            'response' => $response,
            'data' => $data,
        ];
    }

    protected function shouldRetryWithFreshPrebook(int $statusCode, array $data): bool
    {
        if ($statusCode !== 409 && $statusCode !== 410 && $statusCode !== 422) {
            return false;
        }

        $message = strtolower(trim((string) ($data['message'] ?? $data['error'] ?? '')));
        if ($message === '') {
            return false;
        }

        return str_contains($message, 'rate no longer available')
            || str_contains($message, 'search again')
            || str_contains($message, 'rate key')
            || str_contains($message, 'token')
            || str_contains($message, 'expired');
    }

    protected function shouldRetryWithPassengerNormalization(int $statusCode, array $data): bool
    {
        if ($statusCode !== 422 && $statusCode !== 409) {
            return false;
        }

        $message = strtolower(trim((string) ($data['message'] ?? $data['error'] ?? '')));
        if ($message === '') {
            return false;
        }

        return str_contains($message, 'passengers does not match')
            || (str_contains($message, 'passenger') && str_contains($message, 'room'));
    }

    protected function normalizePassengerPayloadForRoomCapacity(array $payload): array
    {
        $roomTypeCode = strtolower(trim((string) ($payload['room_type_code'] ?? '')));
        $inferredRoomOccupancy = 0;

        if (str_contains($roomTypeCode, 'sgl') || str_contains($roomTypeCode, 'single')) {
            $inferredRoomOccupancy = 1;
        } elseif (str_contains($roomTypeCode, 'tpl') || str_contains($roomTypeCode, 'triple')) {
            $inferredRoomOccupancy = 3;
        } elseif (str_contains($roomTypeCode, 'qd') || str_contains($roomTypeCode, 'quad')) {
            $inferredRoomOccupancy = 4;
        } elseif (str_contains($roomTypeCode, 'dbl') || str_contains($roomTypeCode, 'twn') || str_contains($roomTypeCode, 'double') || str_contains($roomTypeCode, 'twin')) {
            $inferredRoomOccupancy = 2;
        }

        if ($inferredRoomOccupancy <= 0) {
            return $payload;
        }

        $rooms = max(1, (int) ($payload['rooms'] ?? 1));
        $expectedTotalPassengers = $inferredRoomOccupancy * $rooms;
        $adults = max(1, (int) ($payload['adults'] ?? 1));
        $children = max(0, (int) ($payload['children'] ?? 0));

        $currentTotalPassengers = $adults + $children;
        if ($currentTotalPassengers < $expectedTotalPassengers) {
            $adults += ($expectedTotalPassengers - $currentTotalPassengers);
        } elseif ($currentTotalPassengers > $expectedTotalPassengers) {
            $overflow = $currentTotalPassengers - $expectedTotalPassengers;
            $reduceChildren = min($children, $overflow);
            $children -= $reduceChildren;
            $overflow -= $reduceChildren;
            if ($overflow > 0) {
                $adults = max(1, $adults - $overflow);
            }
        }

        $payload['occupancy'] = $inferredRoomOccupancy;
        $payload['adults'] = max(1, $adults);
        $payload['children'] = max(0, $children);

        return $payload;
    }
}
