<?php

namespace Modules\Space\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Space\DTOs\SpaceOfferDto;

/** @mixin SpaceOfferDto */
class SpaceOfferResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'type'              => $this->type,

            // Location
            'city'              => $this->city,
            'country'           => $this->country,
            'address'           => $this->address,
            'latitude'          => $this->latitude,
            'longitude'         => $this->longitude,

            // Details
            'description'       => $this->description,
            'short_description' => $this->shortDescription,
            'max_guests'        => $this->maxGuests,
            'bedrooms'          => $this->bedrooms,
            'bathrooms'         => $this->bathrooms,
            'beds'              => $this->beds,

            // Policies
            'check_in_time'     => $this->checkInTime,
            'check_out_time'    => $this->checkOutTime,

            // Media
            'images'            => $this->images,

            // Content
            'amenities'         => $this->amenityNames,

            // Base pricing
            'base_price_per_night' => $this->basePricePerNight,
            'base_currency'        => $this->baseCurrency,
            'base_total_price'     => $this->baseTotalPrice,

            // Converted pricing
            'converted_price_per_night' => $this->convertedPricePerNight,
            'converted_currency'        => $this->convertedCurrency,
            'converted_total_price'     => $this->convertedTotalPrice,

            // Fees
            'cleaning_fee'     => $this->cleaningFee,
            'service_fee'      => $this->serviceFee,

            'is_discounted'    => $this->isDiscounted,
        ];
    }
}
