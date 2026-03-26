<?php

namespace Modules\Hotel\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Hotel\DTOs\HotelOfferDto;
use Modules\Hotel\DTOs\HotelRoomOfferDto;

/** @mixin HotelOfferDto */
class HotelOfferResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                => $this->offerId,
            'provider'          => $this->provider->value,
            'db_hotel_id'       => $this->dbHotelId,
            'slug'              => $this->slug,

            // Basic info
            'name'              => $this->name,
            'star_rating'       => $this->starRating,
            'city'              => $this->city,
            'country'           => $this->country,
            'address'           => $this->address,
            'description'       => $this->description,
            'short_description' => $this->shortDescription,

            // Policies
            'check_in_time'     => $this->checkInTime,
            'check_out_time'    => $this->checkOutTime,

            // Location
            'latitude'          => $this->latitude,
            'longitude'         => $this->longitude,

            // Media
            'images'            => $this->images,

            // Content
            'amenities'         => $this->amenityNames,
            'services'          => $this->serviceNames,

            // Pricing
            'lowest_price'      => $this->lowestPrice,
            'currency'          => $this->displayCurrency,

            // Rooms
            'rooms'             => collect($this->rooms)->map(fn (HotelRoomOfferDto $r) => [
                'id'                => $r->roomId,
                'name'              => $r->name,
                'room_type'         => $r->roomType,
                'bed_configuration' => $r->bedConfiguration,
                'max_adults'        => $r->maxAdults,
                'max_children'      => $r->maxChildren,
                'base_price'        => $r->basePrice,
                'total_price'       => $r->totalPrice,
                'nights'            => $r->nights,
                'currency'          => $r->displayCurrency,
                'is_available'      => $r->isAvailable,
                'amenities'         => $r->amenityNames,
                'size_sqm'          => $r->sizeSqm,
                'view_type'         => $r->viewType,
                'description'       => $r->description,
                'images'            => $r->images,
                'deal_id'           => $r->dealId,           // non-null = price came from a deal
                'original_price'    => $r->originalPrice,    // RoomType price before deal (for "was $X" display)
            ])->values(),

            'badge'             => $this->badge,
            'api_source'        => $this->apiSource,
        ];
    }
}
