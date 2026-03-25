<?php

namespace Modules\Hotel\Providers\Local;

use Modules\Hotel\DTOs\HotelOfferDto;
use Modules\Hotel\DTOs\HotelRoomOfferDto;
use Modules\Hotel\Enums\HotelProviderEnum;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelRoom;

class LocalHotelMapper
{
    public function toOfferDto(Hotel $hotel, int $nights, string $currency, int $adults = 1): HotelOfferDto
    {
        $images = collect([$hotel->featured_image_url, $hotel->banner_image_url])
            ->merge($hotel->gallery_urls ?? [])
            ->filter()
            ->unique()
            ->values()
            ->all();

        $rooms = $hotel->rooms
            ->filter(fn (HotelRoom $r) => $r->is_active)
            ->map(fn (HotelRoom $r) => $this->toRoomOfferDto($r, $nights, $currency, $adults))
            ->values()
            ->all();

        $lowestPrice = collect($rooms)
            ->pluck('basePrice')
            ->filter(fn ($price) => (float) $price > 0)
            ->min() ?? (float) ($hotel->sale_price ?: $hotel->base_price ?: 0);

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

    public function toRoomOfferDto(HotelRoom $room, int $nights, string $currency, int $adults = 1): HotelRoomOfferDto
    {
        $images = collect([$room->image_url])
            ->merge($room->gallery_urls)
            ->filter()
            ->unique()
            ->values()
            ->all();
        $roomType = $room->roomType;
        $basePrice = $adults <= 1
            ? (float) ($roomType?->price_sgl_bb ?: $roomType?->price_dbl_bb ?: 0)
            : (float) ($roomType?->price_dbl_bb ?: $roomType?->price_sgl_bb ?: 0);
        if ($basePrice <= 0) {
            $basePrice = (float) ($room->hotel?->sale_price ?: $room->hotel?->base_price ?: 0);
        }
        $amenityNames = $roomType?->amenities?->pluck('name')->all() ?? [];

        return new HotelRoomOfferDto(
            roomId:           (string) $room->id,
            name:             $room->display_name,
            roomType:         $roomType?->name ?? $room->display_name,
            bedConfiguration: (array) ($roomType?->bed_configuration ?? []),
            maxAdults:        (int) ($roomType?->max_adults ?? 2),
            maxChildren:      (int) ($roomType?->max_children ?? 0),
            basePrice:        $basePrice,
            totalPrice:       (float) ($basePrice * $nights),
            nights:           $nights,
            currency:         $currency,
            isAvailable:      $room->is_active,
            amenityNames:     $amenityNames,
            sizeSqm:          $roomType?->size_sqm ? (float) $roomType->size_sqm : null,
            viewType:         $roomType?->view_type,
            description:      $roomType?->description,
            images:           $images,
        );
    }
}
