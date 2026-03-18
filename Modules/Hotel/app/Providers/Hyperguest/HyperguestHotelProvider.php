<?php

namespace Modules\Hotel\Providers\Hyperguest;

use Carbon\Carbon;
use Modules\Hotel\DTOs\HotelOfferDto;
use Modules\Hotel\DTOs\HotelOrderDto;
use Modules\Hotel\DTOs\PrebookHotelDto;
use Modules\Hotel\DTOs\SearchHotelDto;
use Modules\Hotel\Enums\HotelProviderEnum;
use Modules\Hotel\Exceptions\HotelException;
use Modules\Hotel\Providers\HotelProviderInterface;

/**
 * Hyperguest hotel provider.
 *
 * Mock mode  → reads from public/data/hyperguest/hotels.json
 *              and public/data/hyperguest/booking_response.json.
 *
 * Live mode  → replace loadData() / callBookingApi() with Http:: calls to:
 *              POST https://[search domain]/2.0/search
 *              POST https://[book  domain]/2.0/booking/create
 */
class HyperguestHotelProvider implements HotelProviderInterface
{
    private HyperguestHotelMapper $mapper;
    private string $hotelsPath;
    private string $bookingResponsePath;

    public function __construct()
    {
        $this->mapper              = new HyperguestHotelMapper();
        $this->hotelsPath          = public_path('data/hyperguest/hotels.json');
        $this->bookingResponsePath = public_path('data/hyperguest/booking_response.json');
    }

    // -------------------------------------------------------------------------
    // HotelProviderInterface
    // -------------------------------------------------------------------------

    public function search(SearchHotelDto $dto): array
    {
        $hotels = $this->loadHotels();
        $nights = $dto->nights();

        return collect($hotels)
            ->filter(fn (array $hotel) => $this->matchesSearch($hotel, $dto))
            ->map(fn (array $hotel) => $this->mapper->toOfferDto($hotel, $nights, $dto->currency))
            ->filter(fn (HotelOfferDto $offer) => $this->matchesPriceFilter($offer, $dto))
            ->values()
            ->all();
    }

    /**
     * Return rooms for a specific hotel.
     * Hyperguest rooms are embedded in the search result; this is a convenience
     * lookup for the /api/hotels/rooms endpoint.
     */
    public function getRooms(
        string $offerId,
        string $cityCode,
        string $checkIn,
        string $checkOut,
        int    $adults,
        int    $children,
    ): array {
        $hotel  = $this->findHotel($offerId);
        $nights = max((int) Carbon::parse($checkIn)->diffInDays($checkOut), 1);

        return collect($hotel['rooms'] ?? [])
            ->map(fn (array $room) => $this->mapper->toRoomOfferDto($room, $nights, 'USD', $hotel))
            ->values()
            ->all();
    }

    /**
     * Validate that the requested room still exists and is available.
     * Decodes the booking key and re-reads the JSON to confirm.
     * When live API is available: call the Hyperguest rate-check endpoint here.
     */
    public function prebook(PrebookHotelDto $dto): HotelOfferDto
    {
        // Decode the opaque roomId back to hotel/room keys
        $keys = $this->mapper->decodeBookingKey($dto->roomId);

        $hotel  = $this->findHotel($keys['hotel_id']);
        $nights = max((int) Carbon::parse($dto->checkIn)->diffInDays($dto->checkOut), 1);

        // Verify the specific room is still available
        $rawRoom = collect($hotel['rooms'] ?? [])
            ->firstWhere('room_id', $keys['room_id']);

        if (! $rawRoom || empty($rawRoom['rates']['is_available'])) {
            throw HotelException::roomUnavailable();
        }

        return $this->mapper->toOfferDto($hotel, $nights, $dto->currency);
    }

    /**
     * Call the Hyperguest booking API (mocked from JSON fixture).
     *
     * Builds the exact Hyperguest POST /2.0/booking/create payload
     * so that swapping the mock for a live Http:: call is trivial.
     *
     * @param  array  $checkoutData  Cached data from prebook step
     * @param  array  $guest         Guest details from CheckoutHotelDto
     * @return array                 Full Hyperguest booking response
     */
    public function book(array $checkoutData, array $guest): array
    {
        $keys = $this->mapper->decodeBookingKey($checkoutData['room_id']);

        // Build the payload that mirrors the live API request
        $payload = [
            'dates' => [
                'from' => $checkoutData['check_in'],
                'to'   => $checkoutData['check_out'],
            ],
            'propertyId' => $keys['property_id'],
            'leadGuest'  => [
                'birthDate' => $guest['birth_date'] ?? '1990-01-01',
                'contact'   => [
                    'address' => $guest['address']  ?? 'N/A',
                    'city'    => $guest['city']      ?? 'N/A',
                    'country' => $guest['country']   ?? 'N/A',
                    'email'   => $guest['email'],
                    'phone'   => $guest['phone'],
                    'state'   => $guest['state']     ?? 'N/A',
                    'zip'     => $guest['zip']        ?? 'N/A',
                ],
                'name' => [
                    'first' => $guest['first_name'],
                    'last'  => $guest['last_name'],
                ],
                'title' => $guest['title'] ?? 'MR',
            ],
            'reference' => [
                'agency' => 'travolyo-' . uniqid(),
            ],
            'rooms' => [
                [
                    'roomCode'      => $keys['room_code'],
                    'rateCode'      => $keys['rate_code'],
                    'expectedPrice' => [
                        'amount'   => $checkoutData['total_price'],
                        'currency' => $checkoutData['currency'],
                    ],
                    'guests' => [
                        [
                            'birthDate' => $guest['birth_date'] ?? '1990-01-01',
                            'name'      => [
                                'first' => $guest['first_name'],
                                'last'  => $guest['last_name'],
                            ],
                            'title' => $guest['title'] ?? 'MR',
                        ],
                    ],
                    'specialRequests' => $checkoutData['special_requests']
                        ? [$checkoutData['special_requests']]
                        : [],
                ],
            ],
            'meta' => [
                ['key' => 'Source', 'value' => 'Travolyo'],
            ],
            'isTest'       => true,   // set to false when going live
            'groupBooking' => false,
        ];

        // ---------------------------------------------------------------
        // MOCK: return fixture response (replace with Http:: when live)
        // ---------------------------------------------------------------
        $response = $this->loadBookingResponse();

        // Stamp dynamic values from the actual request into the mock response
        $response['reference']              = $payload['reference'];
        $response['content']['dates']       = $payload['dates'];
        $response['leadGuest']['name']      = $payload['leadGuest']['name'];
        $response['leadGuest']['contact']['email'] = $guest['email'];
        $response['leadGuest']['contact']['phone'] = $guest['phone'];

        if (! empty($response['rooms'][0])) {
            $response['rooms'][0]['roomCode'] = $keys['room_code'];
            $response['rooms'][0]['rateCode'] = $keys['rate_code'];
            $response['rooms'][0]['propertyId'] = $keys['property_id'];
        }

        // Store the payload in the response so it can be logged / audited
        $response['_request_payload'] = $payload;

        return $response;
    }

    /**
     * Retrieve a Hyperguest booking by our internal booking reference.
     * Currently reads from the local Booking meta (no live API call yet).
     */
    public function getOrder(string $orderId): HotelOrderDto
    {
        $booking = \App\Models\Booking::with('meta')
            ->where('code', $orderId)
            ->where('object_model', 'hotel')
            ->firstOrFail();

        $meta     = $booking->getMeta('hotel_details') ?? [];
        $hgData   = $booking->getMeta('hyperguest_booking') ?? [];

        return new HotelOrderDto(
            orderId:         $booking->code,
            provider:        HotelProviderEnum::Hyperguest,
            hotelName:       $meta['hotel_name']  ?? 'N/A',
            roomName:        $meta['room_name']   ?? 'N/A',
            checkIn:         $booking->start_date->toDateString(),
            checkOut:        $booking->end_date->toDateString(),
            nights:          $booking->start_date->diffInDays($booking->end_date),
            adults:          $meta['adults']      ?? 1,
            children:        $meta['children']    ?? 0,
            totalPrice:      (float) $booking->total,
            currency:        $booking->currency,
            status:          $hgData['content']['status'] ?? $booking->status,
            guestFirstName:  $booking->first_name,
            guestLastName:   $booking->last_name,
            guestEmail:      $booking->email,
            guestPhone:      $booking->phone,
            specialRequests: $booking->customer_notes,
        );
    }

    /**
     * Cancel a Hyperguest booking.
     * Currently updates only the local Booking record.
     * When live: call DELETE /2.0/booking/{bookingId} first, then update locally.
     */
    public function cancelOrder(string $orderId): bool
    {
        $booking = \App\Models\Booking::where('code', $orderId)
            ->where('object_model', 'hotel')
            ->firstOrFail();

        $booking->update(['status' => 'cancelled']);

        return true;
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function loadHotels(): array
    {
        if (! file_exists($this->hotelsPath)) {
            throw new \RuntimeException("Hyperguest hotels fixture not found at [{$this->hotelsPath}].");
        }

        $decoded = json_decode(file_get_contents($this->hotelsPath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Hyperguest hotels.json is invalid: ' . json_last_error_msg());
        }

        return $decoded['hotels'] ?? [];
    }

    private function loadBookingResponse(): array
    {
        if (! file_exists($this->bookingResponsePath)) {
            throw new \RuntimeException("Hyperguest booking fixture not found at [{$this->bookingResponsePath}].");
        }

        $decoded = json_decode(file_get_contents($this->bookingResponsePath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Hyperguest booking_response.json is invalid: ' . json_last_error_msg());
        }

        return $decoded;
    }

    private function findHotel(string $hotelId): array
    {
        $hotel = collect($this->loadHotels())->firstWhere('hotel_id', $hotelId);

        if (! $hotel) {
            throw HotelException::notFound(0);
        }

        return $hotel;
    }

    private function matchesSearch(array $hotel, SearchHotelDto $dto): bool
    {
        if (! str_contains(mb_strtolower($hotel['city'] ?? ''), mb_strtolower($dto->city))) {
            return false;
        }

        if ($dto->starRating && (int) ($hotel['star_rating'] ?? 0) !== $dto->starRating) {
            return false;
        }

        return true;
    }

    private function matchesPriceFilter(HotelOfferDto $offer, SearchHotelDto $dto): bool
    {
        if ($dto->priceMin !== null && $offer->lowestPrice < $dto->priceMin) {
            return false;
        }

        if ($dto->priceMax !== null && $offer->lowestPrice > $dto->priceMax) {
            return false;
        }

        return true;
    }
}
