<?php

namespace Modules\Hotel\Providers\Local;

use App\Models\Booking;
use Carbon\Carbon;
use Modules\Hotel\DTOs\HotelOfferDto;
use Modules\Hotel\DTOs\HotelOrderDto;
use Modules\Hotel\DTOs\PrebookHotelDto;
use Modules\Hotel\DTOs\SearchHotelDto;
use Modules\Hotel\Enums\BookingRoomStatusEnum;
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

        if ($dto->amenities) {
            $query->whereHas('amenities', fn ($q) => $q->whereIn('amenities.name', $dto->amenities));
        }

        $hotels = $query->get();
        

        return $hotels
            ->map(fn (Hotel $hotel) => $this->mapper->toOfferDto(
                $hotel, $nights, $dto->currency, $dto->adults, $dto->checkIn, $dto->checkOut
            ))
            ->filter(function (HotelOfferDto $offer) use ($dto) {
                if ($dto->priceMin !== null && $offer->convertedLowestPrice < $dto->priceMin) {
                    return false;
                }

                if ($dto->priceMax !== null && $offer->convertedLowestPrice > $dto->priceMax) {
                    return false;
                }

                return true;
            })
            ->all();
    }

    public function getRooms(
        string $offerId,
        string $cityCode,
        string $checkIn,
        string $checkOut,
        int    $adults,
        int    $children,
        string $currency = 'USD',
    ): array {
        $hotel = Hotel::active()
            ->with(['rooms' => fn ($q) => $q->active()->with('roomType.amenities')])
            ->find((int) $offerId);

        if (! $hotel) {
            return [];
        }

        $nights       = max(1, (int) \Carbon\Carbon::parse($checkIn)->diffInDays(\Carbon\Carbon::parse($checkOut)));
        $baseCurrency = (string) ($hotel->currency ?: $currency);

        return $hotel->rooms
            ->map(fn (HotelRoom $r) => $this->mapper->toRoomOfferDto(
                $r, $nights, $baseCurrency, $currency, $adults, $checkIn, $checkOut
            ))
            ->values()
            ->all();
    }

    public function prebook(PrebookHotelDto $dto): HotelOfferDto
    {
        $room = HotelRoom::active()
            ->with(['hotel.amenities', 'hotel.services', 'roomType.amenities'])
            ->find((int) $dto->roomId);

        if (! $room) {
            throw HotelException::roomNotFound((int) $dto->roomId);
        }

        $nights = (int) $dto->nights();

        $room->loadMissing(['hotel.amenities', 'hotel.services', 'hotel.rooms.roomType']);

        // Pass check-in/check-out so the mapper can apply deal pricing
        return $this->mapper->toOfferDto(
            $room->hotel, $nights, $dto->currency, $dto->adults, $dto->checkIn, $dto->checkOut
        );
    }

    public function book(Booking $booking, array $checkoutData, array $guest): void
    {
        $room = HotelRoom::find((int) ($checkoutData['room_id'] ?? 0));

        if (! $room) {
            return;
        }

        $checkIn  = Carbon::parse($checkoutData['check_in']);
        $checkOut = Carbon::parse($checkoutData['check_out']);
        $nights   = max(1, (int) $checkIn->diffInDays($checkOut));

        BookingRoom::create([
            'booking_id'       => $booking->id,
            'hotel_room_id'    => $room->id,
            'hotel_deal_id'    => $checkoutData['deal_id'] ?? null,
            'check_in'         => $checkIn->toDateString(),
            'check_out'        => $checkOut->toDateString(),
            'nights'           => $nights,
            'adults'           => $checkoutData['adults'] ?? 1,
            'children'         => $checkoutData['children'] ?? 0,
            'unit_price'       => $checkoutData['unit_price'] ?? 0,
            'total_price'      => $checkoutData['total_price'] ?? 0,
            'extra_services'   => $checkoutData['extra_services'] ?? null,
            'special_requests' => $booking->customer_notes,
            'status'           => BookingRoomStatusEnum::Pending->value,
        ]);
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
            status:         (string) ($booking->status ?? ''),
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
