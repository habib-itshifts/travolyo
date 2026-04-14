<?php

namespace Modules\Space\Providers\Local;

use Modules\Space\DTOs\SpaceOfferDto;
use Modules\Space\Models\Space;

class LocalSpaceMapper
{
    /**
     * Map a Space model + pre-calculated pricing into a SpaceOfferDto.
     *
     * Pricing values are calculated by the provider — the mapper only shapes data.
     */
    public function toOfferDto(Space $space, array $pricing): SpaceOfferDto
    {
        return new SpaceOfferDto(
            id:                     $space->id,
            name:                   $space->name,
            slug:                   $space->slug,
            type:                   $space->type,
            city:                   $space->city ?? '',
            country:                $space->country ?? '',
            address:                $space->address,
            description:            $space->description,
            shortDescription:       $space->short_description,
            maxGuests:              $space->max_guests,
            bedrooms:               $space->bedrooms,
            bathrooms:              $space->bathrooms,
            beds:                   $space->beds,
            checkInTime:            $space->check_in_time,
            checkOutTime:           $space->check_out_time,
            latitude:               $space->latitude ? (float) $space->latitude : null,
            longitude:              $space->longitude ? (float) $space->longitude : null,
            images:                 $space->orderedImages(),
            amenityNames:           $space->amenities->pluck('name')->all(),
            basePricePerNight:      $pricing['price_per_night'],
            baseCurrency:           $pricing['base_currency'],
            convertedPricePerNight: $pricing['converted_price_per_night'],
            convertedCurrency:      $pricing['converted_currency'],
            cleaningFee:            $pricing['cleaning_fee'],
            serviceFee:             $pricing['service_fee'],
            baseTotalPrice:         $pricing['total_price'],
            convertedTotalPrice:    $pricing['converted_total_price'],
            isDiscounted:           $space->sale_price && $space->sale_price < $space->price_per_night,
        );
    }
}
