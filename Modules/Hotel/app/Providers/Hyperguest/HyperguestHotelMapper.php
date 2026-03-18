<?php

namespace Modules\Hotel\Providers\Hyperguest;

use Carbon\Carbon;
use Modules\Hotel\DTOs\HotelOfferDto;
use Modules\Hotel\DTOs\HotelRoomOfferDto;
use Modules\Hotel\Enums\HotelProviderEnum;

class HyperguestHotelMapper
{
    /**
     * Map a raw Hyperguest hotel array (from JSON) to HotelOfferDto.
     *
     * @param  array  $hotel   One element from hotels.json "hotels" array
     * @param  int    $nights  Number of nights for the stay
     * @param  string $currency  Requested currency (JSON uses USD; conversion not applied here)
     */
    public function toOfferDto(array $hotel, int $nights, string $currency): HotelOfferDto
    {
        $rooms = collect($hotel['rooms'] ?? [])
            ->map(fn (array $room) => $this->toRoomOfferDto($room, $nights, $currency))
            ->values()
            ->all();

        $lowestPrice = collect($rooms)->min(fn (HotelRoomOfferDto $r) => $r->basePrice) ?? 0.0;

        return new HotelOfferDto(
            offerId:          $hotel['hotel_id'],
            provider:         HotelProviderEnum::Hyperguest,
            name:             $hotel['name'],
            starRating:       (int) $hotel['star_rating'],
            city:             $hotel['city'],
            country:          $hotel['country'],
            address:          $hotel['address'],
            description:      $hotel['description'] ?? null,
            shortDescription: $hotel['short_description'] ?? null,
            checkInTime:      $hotel['check_in_time'] ?? null,
            checkOutTime:     $hotel['check_out_time'] ?? null,
            latitude:         isset($hotel['latitude'])  ? (float) $hotel['latitude']  : null,
            longitude:        isset($hotel['longitude']) ? (float) $hotel['longitude'] : null,
            images:           $hotel['images'] ?? [],
            amenityNames:     $hotel['amenities'] ?? [],
            serviceNames:     [],
            lowestPrice:      (float) $lowestPrice,
            currency:         $currency,
            rooms:            $rooms,
        );
    }

    /**
     * Map a raw Hyperguest room array to HotelRoomOfferDto.
     *
     * @param  array  $room    One element from hotel "rooms" array
     * @param  int    $nights  Number of nights
     * @param  string $currency  Requested currency
     */
    public function toRoomOfferDto(array $room, int $nights, string $currency): HotelRoomOfferDto
    {
        $rates       = $room['rates'] ?? [];
        $basePrice   = (float) ($rates['base_price_per_night'] ?? 0);
        $totalPrice  = $basePrice * max($nights, 1);
        $isAvailable = (bool) ($rates['is_available'] ?? false);

        return new HotelRoomOfferDto(
            roomId:           $room['room_id'],
            name:             $room['name'],
            roomType:         $room['room_type'] ?? 'standard',
            bedConfiguration: (array) ($room['bed_configuration'] ?? []),
            maxAdults:        (int) ($room['max_adults'] ?? 2),
            maxChildren:      (int) ($room['max_children'] ?? 0),
            basePrice:        $basePrice,
            totalPrice:       $totalPrice,
            nights:           $nights,
            currency:         $currency,
            isAvailable:      $isAvailable,
            amenityNames:     $room['amenities'] ?? [],
            sizeSqm:          isset($room['size_sqm']) ? (float) $room['size_sqm'] : null,
            viewType:         $room['view_type'] ?? null,
            description:      $room['description'] ?? null,
            images:           $room['images'] ?? [],
        );
    }
}
