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
    public function toOfferDtoFromDb(array $db): HotelOfferDto
    {
        $galleryUrls = $db['gallery_urls'] ?? null;
        if (is_string($galleryUrls)) {
            $galleryUrls = json_decode($galleryUrls, true);
        }
        $images = is_array($galleryUrls) ? array_values(array_filter($galleryUrls)) : [];

        return new HotelOfferDto(
            offerId:              (string) ($db['external_id'] ?? ''),
            provider:             HotelProviderEnum::Hyperguest,
            name:                 (string) ($db['name'] ?? ''),
            starRating:           (int) ($db['star_rating'] ?? 0),
            city:                 (string) ($db['city'] ?? ''),
            country:              (string) ($db['country'] ?? ''),
            address:              (string) ($db['address'] ?? ''),
            description:          $db['description'] ?? null,
            shortDescription:     $db['short_description'] ?? null,
            checkInTime:          $db['check_in_time'] ?? null,
            checkOutTime:         $db['check_out_time'] ?? null,
            latitude:             isset($db['latitude']) ? (float) $db['latitude'] : null,
            longitude:            isset($db['longitude']) ? (float) $db['longitude'] : null,
            images:               $images,
            amenityNames:         [],
            serviceNames:         [],
            baseLowestPrice:      (float) ($db['base_price'] ?? 0),
            baseCurrency:         (string) ($db['currency'] ?? 'USD'),
            convertedLowestPrice: (float) ($db['sale_price'] ?? $db['base_price'] ?? 0),
            convertedCurrency:    (string) ($db['currency'] ?? 'USD'),
            rooms:                [],
            dbHotelId:            isset($db['id']) ? (int) $db['id'] : null,
            slug:                 $db['slug'] ?? null,
        );
    }

    public function toOfferDto(array $hotel, int $nights, string $currency): HotelOfferDto
    {
        $property = $hotel['propertyInfo'] ?? [];
        $db = $hotel['_db'] ?? [];

        $rooms = collect($hotel['rooms'] ?? [])
            ->flatMap(fn (array $room) => $this->toRoomOfferDtos($room, $nights, $currency, $hotel))
            ->values()
            ->all();

        $baseLowestPrice = collect($rooms)->min(fn (HotelRoomOfferDto $room) => $room->baseCurrentPrice) ?? 0.0;
        $convertedLowestPrice = collect($rooms)->min(fn (HotelRoomOfferDto $room) => $room->convertedCurrentPrice) ?? 0.0;
        $baseCurrency = (string) (collect($rooms)->first()?->baseCurrency ?? $currency);

        $galleryUrls = $db['gallery_urls'] ?? null;
        if (is_string($galleryUrls)) {
            $galleryUrls = json_decode($galleryUrls, true);
        }
        $images = is_array($galleryUrls) ? array_values(array_filter($galleryUrls)) : [];

        return new HotelOfferDto(
            offerId:              (string) ($hotel['propertyId'] ?? ''),
            provider:             HotelProviderEnum::Hyperguest,
            name:                 (string) ($db['name'] ?? $property['name'] ?? ''),
            starRating:           (int) ($db['star_rating'] ?? $property['starRating'] ?? 0),
            city:                 (string) ($db['city'] ?? $property['cityName'] ?? ''),
            country:              (string) ($db['country'] ?? $property['countryCode'] ?? ''),
            address:              (string) ($db['address'] ?? ''),
            description:          $db['description'] ?? null,
            shortDescription:     $db['short_description'] ?? null,
            checkInTime:          $db['check_in_time'] ?? null,
            checkOutTime:         $db['check_out_time'] ?? null,
            latitude:             isset($db['latitude']) ? (float) $db['latitude'] : (isset($property['latitude']) ? (float) $property['latitude'] : null),
            longitude:            isset($db['longitude']) ? (float) $db['longitude'] : (isset($property['longitude']) ? (float) $property['longitude'] : null),
            images:               $images,
            amenityNames:         [],
            serviceNames:         [],
            baseLowestPrice:      (float) $baseLowestPrice,
            baseCurrency:         $baseCurrency,
            convertedLowestPrice: (float) $convertedLowestPrice,
            convertedCurrency:    $currency,
            rooms:                $rooms,
            dbHotelId:            isset($db['id']) ? (int) $db['id'] : null,
            slug:                 $db['slug'] ?? null,
        );
    }

    
    /**
     * One API room can contain multiple rate plans,
     * so return multiple HotelRoomOfferDto items.
     */
    public function toRoomOfferDtos(array $room, int $nights, string $currency, array $hotel = []): array
    {
        $ratePlans = $room['ratePlans'] ?? [];

        return collect($ratePlans)
            ->map(fn (array $ratePlan) => $this->toRoomOfferDto($room, $ratePlan, $nights, $currency, $hotel))
            ->filter()
            ->values()
            ->all();
    }

    public function toRoomOfferDto(
        array $room,
        array $ratePlan,
        int $nights,
        string $currency,
        array $hotel = []
    ): ?HotelRoomOfferDto {
        $basePrice = data_get($ratePlan, 'prices.sell.price');
        $baseBarPrice = data_get($ratePlan, 'prices.bar.price');

        $baseCurrency = data_get($ratePlan, 'prices.sell.currency');

        if ($basePrice <= 0) {
            return null;
        }

        $baseTotalPrice = $basePrice * $nights;

        $convertedPrice = currency($basePrice, $baseCurrency, $currency, false);
        // $convertedPrice = $baseTotalPrice;

        $convertedTotalPrice = $convertedPrice * $nights;

        $isAvailable = ((int) ($room['numberOfAvailableRooms'] ?? 0)) > 0;

        $bookingPayload = [
            'hotel_id'      => $hotel['propertyId'] ?? null,
            'property_id'   => $hotel['propertyId'] ?? null,
            'room_id'       => $room['roomId'] ?? null,
            'room_code'     => $room['roomTypeCode'] ?? null,
            'rate_plan_id'  => $ratePlan['ratePlanId'] ?? null,
            'rate_code'     => $ratePlan['ratePlanCode'] ?? null,
            'price'         => $basePrice,
            'currency'      => $baseCurrency,
            'meal_plan'     => $ratePlan['board'] ?? null,
            'is_immediate'  => $ratePlan['isImmediate'] ?? false,
        ];

        $bookingKey = base64_encode(json_encode($bookingPayload));

        $bedConfiguration = collect($room['settings']['beddingConfigurations'] ?? [])
            ->map(fn (array $bed) => trim(($bed['quantity'] ?? 1) . 'x ' . ($bed['type'] ?? 'Bed')))
            ->values()
            ->all();

        $amenities = collect([
            $ratePlan['board'] ?? null,
            data_get($ratePlan, 'payment.charge'),
            data_get($ratePlan, 'payment.chargeType'),
        ])->filter()->values()->all();

        $descriptionParts = array_filter([
            $ratePlan['ratePlanName'] ?? null,
            !empty($ratePlan['remarks']) ? implode("\n", $ratePlan['remarks']) : null,
        ]);

        return new HotelRoomOfferDto(
            roomId:                 $bookingKey,
            // name:                   (string) (($room['roomName'] ?? 'Room') . ' - ' . ($ratePlan['ratePlanName'] ?? 'Rate')),
            name:                   (string) (($room['roomName'] ?? 'Room')),
            roomType:               (string) ($room['roomTypeCode'] ?? 'standard'),
            bedConfiguration:       $bedConfiguration,
            maxAdults:              (int) ($room['settings']['maxAdultsNumber'] ?? 2),
            maxChildren:            (int) ($room['settings']['maxChildrenNumber'] ?? 0),
            // baseOriginalPrice:      (float) $baseBarPrice,
            // convertedOriginalPrice: currency($baseBarPrice, $baseCurrency, $currency, false),
            baseOriginalPrice:      $basePrice,
            convertedOriginalPrice: $convertedPrice,
            baseCurrentPrice:       $basePrice,
            convertedCurrentPrice:  $convertedPrice,
            baseTotalPrice:         $baseTotalPrice,
            convertedTotalPrice:    $convertedTotalPrice,
            nights:                 $nights,
            baseCurrency:           $baseCurrency,
            convertedCurrency:      $currency,
            isAvailable:            $isAvailable,
            amenityNames:           $amenities,
            sizeSqm:                isset($room['settings']['roomSize']) ? (float) $room['settings']['roomSize'] : null,
            viewType:               null,
            description:            !empty($descriptionParts) ? implode("\n\n", $descriptionParts) : null,
            images:                 [],
            dealId:                 isset($ratePlan['ratePlanId']) ? (string) $ratePlan['ratePlanId'] : null,
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




















