<?php

namespace Modules\Hotel\Providers\Local;

use Modules\Hotel\DTOs\HotelOfferDto;
use Modules\Hotel\DTOs\HotelOrderDto;
use Modules\Hotel\DTOs\PrebookHotelDto;
use Modules\Hotel\DTOs\SearchHotelDto;
use Modules\Hotel\Enums\HotelProviderEnum;
use Modules\Hotel\Exceptions\HotelException;
use Modules\Hotel\Models\BookingRoom;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelRoom;
use Modules\Hotel\Providers\HotelProviderInterface;

class LocalHotelProvider implements HotelProviderInterface
{
    private LocalHotelMapper $mapper;

    public function __construct()
    {
        $this->mapper = new LocalHotelMapper();
    }

    public function search(SearchHotelDto $dto): array
    {
        $nights = $dto->nights();

        $query = Hotel::query()
            ->active()
            ->with([
                'amenities',
                'services',
                'rooms' => fn ($q) => $q->active()->with('roomType.amenities'),
            ])
            ->where(function ($query) use ($dto) {
                $query
                    ->where('city', 'like', '%' . $dto->destination . '%')
                    ->orWhere('country', 'like', '%' . $dto->destination . '%');
            });

        if ($dto->starRating) {
            $query->where('star_rating', $dto->starRating);
        }

        if ($dto->amenityIds) {
            $query->whereHas('amenities', fn ($q) => $q->whereIn('amenities.id', $dto->amenityIds));
        }

        if ($dto->priceMin !== null || $dto->priceMax !== null) {
            $query->whereHas('rooms', function ($q) use ($dto) {
                if ($dto->priceMin !== null) {
                    $q->where('base_price', '>=', $dto->priceMin);
                }
                if ($dto->priceMax !== null) {
                    $q->where('base_price', '<=', $dto->priceMax);
                }
            });
        }

        $hotels = $query->get();

        // Pass check-in/check-out so the mapper can look up deal pricing
        return $hotels
            ->map(fn (Hotel $h) => $this->mapper->toOfferDto(
                $h, $nights, $dto->currency, $dto->adults, $dto->checkIn, $dto->checkOut
            ))
            ->all();
    }

    public function getRooms(
        string $offerId,
        string $cityCode,
        string $checkIn,
        string $checkOut,
        int    $adults,
        int    $children,
    ): array {
        // Local hotels include rooms directly in search() results via LocalHotelMapper.
        return [];
    }

    public function prebook(PrebookHotelDto $dto): HotelOfferDto
    {
        $room = HotelRoom::active()
            ->with(['hotel.amenities', 'hotel.services', 'roomType.amenities'])
            ->find((int) $dto->roomId);

        if (! $room) {
            throw HotelException::roomNotFound((int) $dto->roomId);
        }

        $nights = (int) now()->parse($dto->checkIn)->diffInDays($dto->checkOut);

        $room->loadMissing(['hotel.amenities', 'hotel.services', 'hotel.rooms.roomType']);

        // Pass check-in/check-out so the mapper can apply deal pricing
        return $this->mapper->toOfferDto(
            $room->hotel, $nights, $dto->currency, $dto->adults, $dto->checkIn, $dto->checkOut
        );
    }

   public function getOrder(string $orderId): HotelOrderDto
    {
        $bookingRoom = BookingRoom::with(['booking.metaItems', 'room.hotel', 'room.roomType'])
            ->whereHas('booking', fn ($q) => $q->where('code', $orderId))
            ->firstOrFail();
        $booking = $bookingRoom->booking;
        $room = $bookingRoom->room;
        $hotelDetails = $booking->getJsonMeta('hotel_details');

        $hotelName = (string) (
            $room?->hotel?->name
            ?? ($hotelDetails['hotel_name'] ?? 'Hotel')
        );

        $roomName = (string) (
            $room?->display_name
            ?? ($hotelDetails['room_name'] ?? 'Room')
        );

        $checkIn = $bookingRoom->check_in?->toDateString()
            ?? ($hotelDetails['check_in'] ?? $booking->start_date?->toDateString() ?? now()->toDateString());

        $checkOut = $bookingRoom->check_out?->toDateString()
            ?? ($hotelDetails['check_out'] ?? $booking->end_date?->toDateString() ?? now()->toDateString());

        $nights = (int) ($bookingRoom->nights ?? 0);
        if ($nights <= 0) {
            $nights = max(1, now()->parse($checkIn)->diffInDays($checkOut));
        }

        return new HotelOrderDto(
            orderId:        $booking->code,
            provider:       HotelProviderEnum::Local,
            hotelName:      $hotelName,
            roomName:       $roomName,
            checkIn:        $checkIn,
            checkOut:       $checkOut,
            nights:         $nights,
            adults:         (int) ($bookingRoom->adults ?? 0),
            children:       (int) ($bookingRoom->children ?? 0),
            totalPrice:     (float) $bookingRoom->total_price,
            currency:       (string) ($booking->currency ?? ($hotelDetails['currency'] ?? 'USD')),
            status:         (string) ($bookingRoom->status ?? 'pending'),
            guestFirstName: (string) ($booking->first_name ?? ''),
            guestLastName:  (string) ($booking->last_name ?? ''),
            guestEmail:     (string) ($booking->email ?? ''),
            guestPhone:     (string) ($booking->phone ?? ''),
            specialRequests:$bookingRoom->special_requests ?? $booking->customer_notes,
        );
    }

    public function cancelOrder(string $orderId): bool
    {
        $bookingRoom = BookingRoom::with('booking')
            ->whereHas('booking', fn ($q) => $q->where('code', $orderId))
            ->firstOrFail();

        $bookingRoom->update(['status' => 'cancelled']);
        $bookingRoom->booking->update(['status' => 'cancelled']);

        return true;
    }
}
