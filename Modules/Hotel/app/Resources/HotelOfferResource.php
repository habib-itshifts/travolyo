<?php

namespace Modules\Hotel\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Hotel\DTOs\HotelOfferDto;

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

            // Backward-compatible display pricing
            'lowest_price'      => $this->convertedLowestPrice,
            'currency'          => $this->convertedCurrency,

            // Base Pricing
            'base_lowest_price'      => $this->baseLowestPrice,
            'base_currency'          => $this->baseCurrency,

            // Convered Pricing
            'converted_lowest_price'      => $this->convertedLowestPrice,
            'converted_currency'          => $this->convertedCurrency,
            'is_discounted'               => $this->isDiscounted(),

            // Rooms
            'rooms'             => HotelRoomOfferResource::collection($this->rooms),

            'badge'             => $this->badge,
            'api_source'        => $this->apiSource,
        ];
    }
}
