<?php

namespace Modules\Hotel\Providers\Local;

use Modules\Hotel\DTOs\HotelOfferDto;
use Modules\Hotel\DTOs\HotelRoomOfferDto;
use Modules\Hotel\Enums\HotelProviderEnum;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelRoom;

class LocalHotelMapper
{
    public function toOfferDto(Hotel $hotel, int $nights, string $currency): HotelOfferDto
    {
        $images = collect([$hotel->featured_image_url, $hotel->banner_image_url])
            ->merge($hotel->gallery_urls ?? [])
            ->filter()
            ->unique()
            ->values()
            ->all();

        $rooms = $hotel->rooms
            ->filter(fn (HotelRoom $r) => $r->is_active)
            ->map(fn (HotelRoom $r) => $this->toRoomOfferDto($r, $nights, $currency))
            ->values()
            ->all();

        $lowestPrice = collect($rooms)->min('basePrice') ?? 0;

        return new HotelOfferDto(
            offerId:          (string) $hotel->id,
            provider:         HotelProviderEnum::Local,
            name:             $hotel->name,
            starRating:       (int) $hotel->star_rating,
            city:             $hotel->city,
            country:          $hotel->country,
            address:          $hotel->address,
            description:      $hotel->description,
            shortDescription: $hotel->short_description,
            checkInTime:      $hotel->check_in_time,
            checkOutTime:     $hotel->check_out_time,
            latitude:         $hotel->latitude ? (float) $hotel->latitude : null,
            longitude:        $hotel->longitude ? (float) $hotel->longitude : null,
            images:           $images,
            amenityNames:     $hotel->amenities->pluck('name')->all(),
            serviceNames:     $hotel->services->pluck('name')->all(),
            lowestPrice:      (float) $lowestPrice,
            currency:         $currency,
            rooms:            $rooms,
            dbHotelId:        $hotel->id,
            slug:             $hotel->slug,
        );
    }

    public function toRoomOfferDto(HotelRoom $room, int $nights, string $currency): HotelRoomOfferDto
    {
        $images = collect([$room->image_url])
            ->merge($room->gallery_urls)
            ->filter()
            ->unique()
            ->values()
            ->all();

        return new HotelRoomOfferDto(
            roomId:           (string) $room->id,
            name:             $room->name,
            roomType:         $room->room_type,
            bedConfiguration: (array) $room->bed_configuration,
            maxAdults:        $room->max_adults,
            maxChildren:      $room->max_children,
            basePrice:        (float) $room->base_price,
            totalPrice:       (float) ($room->base_price * $nights),
            nights:           $nights,
            currency:         $currency,
            isAvailable:      $room->is_active,
            amenityNames:     $room->amenities->pluck('name')->all(),
            sizeSqm:          $room->size_sqm ? (float) $room->size_sqm : null,
            viewType:         $room->view_type,
            description:      $room->description,
            images:           $images,
        );
    }
}
