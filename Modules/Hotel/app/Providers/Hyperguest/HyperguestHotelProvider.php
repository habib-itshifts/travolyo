<?php

namespace Modules\Hotel\Providers\Hyperguest;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
    protected array $headers;

    public function __construct()
    {
        $this->mapper = new HyperguestHotelMapper();
        $this->headers = [
            'Accept-Encoding' => 'gzip, deflate',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . config('hotel.hyperguest.api_key'),
        ];
    }

    public function search(SearchHotelDto $dto): array
    {
        $destination = strtolower(trim($dto->destination));

        $query = DB::table('external_hyperguest_hotels')
            ->where('status', 'active')
            ->where(function ($q) use ($destination) {
                $q->whereRaw('LOWER(city) = ?', [$destination])
                  ->orWhereRaw('LOWER(country) = ?', [$destination]);
            });

        if ($dto->starRating !== null) {
            $query->where('star_rating', $dto->starRating);
        }


        return $query->get()
            ->map(fn ($row) => $this->mapper->toSearchOfferDto((array) $row))
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
            ->flatMap(fn (array $room) => $this->mapper->toRoomOfferDtos($room, $nights, $currency, $hotel, $adults, $children))
            ->values()
            ->all();
    }

    public function prebook(PrebookHotelDto $dto): HotelOfferDto
    {
        
        $keys = $this->mapper->decodeBookingKey($dto->roomId);

        //Call Hyperguest pre-book API to confirm price/availability
        $payload = [
            'search' => [
                'dates' => [
                    'from' => $dto->checkIn,
                    'to' => $dto->checkOut,
                ],
                'propertyId' => $dto->offerId,
                'nationality' => config('hotel.default_nationality', 'AE'),
                'pax' => [
                    [
                        'adults' => $dto->adults,
                        'children' => [], // TODO: add child ages when children logic is implemented
                    ],
                ],
            ],
            'rooms' => [
                [
                    'roomCode' => $keys['room_code'],
                    'rateCode' => $keys['rate_code'],
                    'expectedPrice' => [
                        'amount' => (float) $keys['price'],
                        'currency' => $keys['currency'],
                    ],
                ],
            ],
        ];
        


        $response = Http::withHeaders($this->headers)
            ->acceptJson()
            ->timeout(30)
            ->post(config('hotel.hyperguest.base_url') . '/booking/pre-book', $payload);
        if ($response->failed()) {
            throw new HotelException(
                'Hyperguest pre-book failed: ' . $response->status() . ' ' . $response->body(),
                $response->status()
            );
        }

        $prebook = $response->json();

        // Also fetch the hotel data for the offer DTO (DB + API enrichment)
        $hotel = $this->findHotel(
            (string) ($keys['property_id'] ?? $keys['hotel_id']),
            $dto->checkIn,
            $dto->checkOut,
            $dto->adults,
            $dto->children,
            $dto->currency
        );

        return $this->mapper->toOfferDto(
            $hotel,
            max((int) Carbon::parse($dto->checkIn)->diffInDays($dto->checkOut), 1),
            $dto->currency
        );
    }

    public function book(Booking $booking, array $checkoutData, array $guest): void
    {
        $keys = $this->mapper->decodeBookingKey($checkoutData['room_id']);
        $payload = [
            'dates' => [
                'from' => $checkoutData['check_in'],
                'to' => $checkoutData['check_out'],
            ],
            'propertyId' => (int) $keys['property_id'],
            'leadGuest' => [
                'birthDate' => $guest['birth_date'] ?? '1990-01-01',
                'contact' => [
                    'address' => $guest['address'] ?? 'N/A',
                    'city' => $guest['city'] ?? 'N/A',
                    'country' => config('hotel.default_nationality', 'AE'),
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
            // TODO: replace test card with real payment details or switch type (credit_balance, external, etc.) for production
            'paymentDetails' => [
                'type' => 'credit_card',
                'details' => [
                    'number' => '4111111111111111', // test card — safe because charge is false
                    'cvv' => '123',
                    'expiry' => [
                        'month' => '12',
                        'year' => '2028',
                    ],
                    'name' => [
                        'first' => $guest['first_name'],
                        'last' => $guest['last_name'],
                    ],
                    'charge' => false, // DO NOT set true unless pre-arranged with HyperGuest
                ],
            ],
            'rooms' => [[
                'roomCode' => $keys['room_code'],
                'rateCode' => $keys['rate_code'],
                'expectedPrice' => [
                    'amount' => (float) $checkoutData['total_price'],
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
                'specialRequests' => ! empty($checkoutData['special_requests'])
                    ? [$checkoutData['special_requests']]
                    : [],
            ]],
            'meta' => [
                ['key' => 'Source', 'value' => 'Travolyo'],
            ],
            'isTest' => config('hotel.hyperguest.is_test', true),
            'groupBooking' => false,
        ];

        $response = Http::withHeaders($this->headers)
            ->acceptJson()
            ->timeout(30)
            ->post(config('hotel.hyperguest.base_url') . '/booking/create', $payload);
        dd($response->json());
        
            if ($response->failed()) {
            Log::error("[HyperguestBooking] Failed for booking {$booking->code}: {$response->status()} {$response->body()}");
            $booking->addMeta('hyperguest_booking_error', $response->body());
            throw new HotelException(
                'Hyperguest booking failed: ' . $response->status() . ' ' . $response->body(),
                $response->status()
            );
        }

        $hgBooking = $response->json();

        $booking->addMeta('hyperguest_booking', $hgBooking);
        $booking->addMeta('hyperguest_booking_id', $hgBooking['bookingId'] ?? null);
        $booking->addMeta('hyperguest_status', $hgBooking['content']['status'] ?? 'unknown');
        $booking->addMeta('hyperguest_cancellation_policy', $hgBooking['rooms'][0]['cancellationPolicy'] ?? []);
        $booking->addMeta('hyperguest_remarks', $hgBooking['rooms'][0]['remarks'] ?? []);
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

    // Fetch live room availability from Hyperguest API
    private function   searchHotelsByIds(
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

        $chunks = collect($hotelIds)->chunk(20)->values();

        $results = [];

        foreach ($chunks as $chunk) {
            $response = Http::withHeaders($this->headers)
                ->acceptJson()
                ->timeout(30)
                ->get('https://search-api.hyperguest.io/2.0/', [
                    'checkIn'             => $checkIn,
                    'nights'              => $nights,
                    'guests'              => $guests,
                    'hotelIds'            => $chunk->implode(','),
                    'customerNationality' => $nationality,
                    'currency'            => $currency,
                ]);

            if ($response->successful()) {
                array_push($results, ...$this->unwrapResults($response->json()));
            }
        }

        return collect($results)
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

}
