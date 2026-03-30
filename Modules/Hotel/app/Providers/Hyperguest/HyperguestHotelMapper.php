<?php

namespace Modules\Hotel\Providers\Hyperguest;

use Modules\Hotel\DTOs\HotelOfferDto;
use Modules\Hotel\DTOs\HotelRoomOfferDto;
use Modules\Hotel\Enums\HotelProviderEnum;

class HyperguestHotelMapper
{
    /**
     * Map a raw Hyperguest hotel array (from JSON) to HotelOfferDto.
     *
     * @param  array  $hotel    One element from hotels.json "hotels" array
     * @param  int    $nights   Number of nights
     * @param  string $currency Requested/converted currency
     */
    public function toOfferDto(array $hotel, int $nights, string $currency): HotelOfferDto
    {
        $rooms = collect($hotel['rooms'] ?? [])
            ->map(fn (array $room) => $this->toRoomOfferDto($room, $nights, $currency, $hotel))
            ->values()
            ->all();

        $baseLowestPrice = collect($rooms)->min(fn (HotelRoomOfferDto $room) => $room->baseCurrentPrice) ?? 0.0;
        $convertedLowestPrice = collect($rooms)->min(fn (HotelRoomOfferDto $room) => $room->convertedCurrentPrice) ?? 0.0;
        $baseCurrency = (string) (collect($rooms)->first()?->baseCurrency ?? $currency);

        return new HotelOfferDto(
            offerId:              $hotel['hotel_id'],
            provider:             HotelProviderEnum::Hyperguest,
            name:                 $hotel['name'],
            starRating:           (int) $hotel['star_rating'],
            city:                 $hotel['city'],
            country:              $hotel['country'],
            address:              $hotel['address'],
            description:          $hotel['description'] ?? null,
            shortDescription:     $hotel['short_description'] ?? null,
            checkInTime:          $hotel['check_in_time'] ?? null,
            checkOutTime:         $hotel['check_out_time'] ?? null,
            latitude:             isset($hotel['latitude']) ? (float) $hotel['latitude'] : null,
            longitude:            isset($hotel['longitude']) ? (float) $hotel['longitude'] : null,
            images:               $hotel['images'] ?? [],
            amenityNames:         $hotel['amenities'] ?? [],
            serviceNames:         [],
            baseLowestPrice:      (float) $baseLowestPrice,
            baseCurrency:         $baseCurrency,
            convertedLowestPrice: (float) $convertedLowestPrice,
            convertedCurrency:    $currency,
            rooms:                $rooms,
        );
    }

    /**
     * Map a raw Hyperguest room array to HotelRoomOfferDto.
     *
     * The roomId is encoded as base64 JSON containing all keys needed
     * for prebook and booking: hotel_id, property_id, room_code, rate_code, price.
     *
     * @param  array  $room       One element from hotel "rooms" array
     * @param  int    $nights     Number of nights
     * @param  string $currency   Requested/converted currency
     * @param  array  $hotel      Parent hotel array (needed for hotel_id / property_id)
     */
    public function toRoomOfferDto(array $room, int $nights, string $currency, array $hotel = []): HotelRoomOfferDto
    {
        $rates = $room['rates'] ?? [];
        $baseCurrency = (string) ($rates['currency'] ?? $currency);
        $basePrice = (float) ($rates['base_price_per_night'] ?? 0);
        $baseTotalPrice = $basePrice * max($nights, 1);
        $convertedPrice = $baseCurrency === $currency
            ? $basePrice
            : (float) currency($basePrice, $baseCurrency, $currency, false);
        $convertedTotalPrice = $convertedPrice * max($nights, 1);
        $isAvailable = (bool) ($rates['is_available'] ?? false);

        $bookingKey = base64_encode(json_encode([
            'hotel_id'    => $hotel['hotel_id'] ?? $room['room_id'],
            'property_id' => $hotel['property_id'] ?? null,
            'room_id'     => $room['room_id'],
            'room_code'   => $room['room_code'] ?? 'STD',
            'rate_code'   => $room['rate_code'] ?? 'BAR',
            'price'       => $basePrice,
            'currency'    => $baseCurrency,
            'meal_plan'   => $rates['meal_plan'] ?? null,
        ]));

        return new HotelRoomOfferDto(
            roomId:                 $bookingKey,
            name:                   $room['name'],
            roomType:               $room['room_type'] ?? 'standard',
            bedConfiguration:       (array) ($room['bed_configuration'] ?? []),
            maxAdults:              (int) ($room['max_adults'] ?? 2),
            maxChildren:            (int) ($room['max_children'] ?? 0),
            baseOriginalPrice:      0.0,
            convertedOriginalPrice: 0.0,
            baseCurrentPrice:       $basePrice,
            convertedCurrentPrice:  $convertedPrice,
            baseTotalPrice:         $baseTotalPrice,
            convertedTotalPrice:    $convertedTotalPrice,
            nights:                 $nights,
            baseCurrency:           $baseCurrency,
            convertedCurrency:      $currency,
            isAvailable:            $isAvailable,
            amenityNames:           $room['amenities'] ?? [],
            sizeSqm:                isset($room['size_sqm']) ? (float) $room['size_sqm'] : null,
            viewType:               $room['view_type'] ?? null,
            description:            $room['description'] ?? null,
            images:                 $room['images'] ?? [],
            dealId:                 null,
        );
    }

    /**
     * Decode a booking key previously generated by toRoomOfferDto().
     *
     * @return array{hotel_id:string, property_id:int|null, room_id:string, room_code:string, rate_code:string, price:float, currency:string, meal_plan:string|null}
     */
    public function decodeBookingKey(string $roomId): array
    {
        $decoded = json_decode(base64_decode($roomId), true);

        if (! is_array($decoded) || empty($decoded['room_code'])) {
            throw new \InvalidArgumentException("Invalid Hyperguest room key: [{$roomId}]");
        }

        return $decoded;
    }
}
