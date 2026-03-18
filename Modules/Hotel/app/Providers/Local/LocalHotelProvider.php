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
            ->with(['amenities', 'services', 'rooms' => fn ($q) => $q->active()->with('amenities')])
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

        return $hotels
            ->map(fn (Hotel $h) => $this->mapper->toOfferDto($h, $nights, $dto->currency))
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
            ->with(['hotel.amenities', 'hotel.services', 'amenities'])
            ->find((int) $dto->roomId);

        if (! $room) {
            throw HotelException::roomNotFound((int) $dto->roomId);
        }

        $nights = (int) now()->parse($dto->checkIn)->diffInDays($dto->checkOut);

        return $this->mapper->toOfferDto($room->hotel, $nights, $dto->currency);
    }

    public function getOrder(string $orderId): HotelOrderDto
    {
        $bookingRoom = BookingRoom::with(['booking', 'room.hotel'])
            ->whereHas('booking', fn ($q) => $q->where('code', $orderId))
            ->firstOrFail();

        return new HotelOrderDto(
            orderId:        $bookingRoom->booking->code,
            provider:       HotelProviderEnum::Local,
            hotelName:      $bookingRoom->room->hotel->name,
            roomName:       $bookingRoom->room->name,
            checkIn:        $bookingRoom->check_in->toDateString(),
            checkOut:       $bookingRoom->check_out->toDateString(),
            nights:         $bookingRoom->nights,
            adults:         $bookingRoom->adults,
            children:       $bookingRoom->children,
            totalPrice:     (float) $bookingRoom->total_price,
            currency:       $bookingRoom->booking->currency,
            status:         $bookingRoom->status,
            guestFirstName: $bookingRoom->booking->first_name,
            guestLastName:  $bookingRoom->booking->last_name,
            guestEmail:     $bookingRoom->booking->email,
            guestPhone:     $bookingRoom->booking->phone,
            specialRequests:$bookingRoom->special_requests,
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
