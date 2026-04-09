<?php

namespace Modules\Hotel\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Hotel\DTOs\HotelRoomOfferDto;

/** @mixin HotelRoomOfferDto */
class HotelRoomOfferResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                       => $this->roomId,
            'name'                     => $this->name,
            'room_type'                => $this->roomType,
            'bed_configuration'        => $this->bedConfiguration,
            'max_adults'               => $this->maxAdults,
            'max_children'             => $this->maxChildren,
            'base_original_price'      => $this->baseOriginalPrice,
            'converted_original_price' => $this->convertedOriginalPrice,
            'base_current_price'       => $this->baseCurrentPrice,
            'converted_current_price'  => $this->convertedCurrentPrice,
            'base_total_price'         => $this->baseTotalPrice,
            'converted_total_price'    => $this->convertedTotalPrice,
            'nights'                   => $this->nights,
            'base_currency'            => $this->baseCurrency,
            'converted_currency'       => $this->convertedCurrency,
            'is_available'             => $this->isAvailable,
            'amenities'                => $this->amenityNames,
            'size_sqm'                 => $this->sizeSqm,
            'view_type'                => $this->viewType,
            'description'              => $this->description,
            'images'                   => $this->images,
            'deal_id'                  => $this->dealId,
            'is_discounted'            => $this->isDiscounted(),
        ];
    }
}