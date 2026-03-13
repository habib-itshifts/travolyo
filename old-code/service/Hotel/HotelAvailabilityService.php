<?php

namespace App\Http\Services\Frontend\Hotel;

use App\Http\Dtos\Frontend\Hotel\RoomAvailabilityDto;
use App\Http\Dtos\Frontend\Hotel\RoomAvailabilityRequestDto;
use Modules\Hotel\Models\HotelRoom;

/**
 * Checks real-time room availability for a hotel.
 *
 * Uses the existing Modules\Hotel\Models\HotelRoom::isAvailableAt() which
 * accounts for:
 *  - Per-date price/availability overrides  (bravo_hotel_room_dates)
 *  - Active bookings that reduce room count  (bravo_hotel_room_bookings)
 *  - iCal import blocks (if configured on the room)
 *  - Adult / children capacity constraints
 *
 * Returns an array of RoomAvailabilityDto, one per published room.
 */
class HotelAvailabilityService
{
    /**
     * Check availability of all published rooms for the given hotel ID.
     *
     * @return RoomAvailabilityDto[]
     */
    public function checkHotelRooms(int $hotelId, RoomAvailabilityRequestDto $dto): array
    {
        $rooms = HotelRoom::where('parent_id', $hotelId)
            ->where('status', 'publish')
            ->get();

        $results = [];

        foreach ($rooms as $room) {
            $results[] = $this->checkRoom($room, $dto);
        }

        return $results;
    }

    /**
     * Check availability for a single room model instance.
     */
    public function checkRoom(HotelRoom $room, RoomAvailabilityRequestDto $dto): RoomAvailabilityDto
    {
        $available = $room->isAvailableAt($dto->toFilters());

        if ($available) {
            $nights        = (int)   ($room->tmp_nights ?? 0);
            $totalPrice    = (float) ($room->tmp_price  ?? 0);
            $pricePerNight = ($nights > 0)
                ? round($totalPrice / $nights, 2)
                : (float) $room->price;

            return new RoomAvailabilityDto(
                room_id:         (int)   $room->id,
                room_name:       (string) $room->title,
                available:       true,
                price_per_night: $pricePerNight,
                total_price:     $totalPrice,
                nights:          $nights,
            );
        }

        return new RoomAvailabilityDto(
            room_id:         (int)   $room->id,
            room_name:       (string) $room->title,
            available:       false,
            price_per_night: (float) $room->price,
            total_price:     0.0,
            nights:          0,
            reason:          'Not available for selected dates',
        );
    }
}
