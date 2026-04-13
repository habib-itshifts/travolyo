<?php

namespace Modules\Space\Actions;

use Carbon\Carbon;
use Modules\Space\DTOs\BookSpaceDto;
use Modules\Space\DTOs\SpaceOfferDto;
use Modules\Space\Exceptions\SpaceException;
use Modules\Space\Models\Space;

/**
 * BookSpaceAction (prebook)
 *
 * Validates availability, calculates pricing, and returns a SpaceOfferDto
 * for the checkout cache flow (same as hotel prebook).
 */
class BookSpaceAction
{
    public function handle(BookSpaceDto $dto): SpaceOfferDto
    {
        $space = Space::with('amenities')->active()->find($dto->spaceId);

        if (! $space) {
            throw SpaceException::notFound($dto->spaceId);
        }

        $checkIn  = Carbon::parse($dto->checkIn);
        $checkOut = Carbon::parse($dto->checkOut);
        $nights   = (int) $checkIn->diffInDays($checkOut);

        if ($checkOut->lte($checkIn)) {
            throw SpaceException::invalidDates();
        }

        if ($space->min_stay_nights && $nights < $space->min_stay_nights) {
            throw SpaceException::minStayNotMet($space->min_stay_nights);
        }

        if ($space->max_stay_nights && $nights > $space->max_stay_nights) {
            throw SpaceException::maxStayExceeded($space->max_stay_nights);
        }

        if (! $space->isAvailableForDates($dto->checkIn, $dto->checkOut)) {
            throw SpaceException::unavailable();
        }

        // Calculate total from per-date pricing
        $totalNightPrice = 0;
        $current = $checkIn->copy();
        while ($current->lt($checkOut)) {
            $totalNightPrice += $space->effectivePriceForDate($current->toDateString());
            $current->addDay();
        }

        $pricePerNight = $totalNightPrice / max($nights, 1);
        $cleaningFee   = (float) ($space->cleaning_fee ?? 0);
        $serviceFee    = (float) ($space->service_fee ?? 0);
        $totalPrice    = $totalNightPrice + $cleaningFee + $serviceFee;

        $baseCurrency = $space->currency ?: 'USD';
        $userCurrency = currency()->getUserCurrency();
        $convertedPrice = ($userCurrency !== $baseCurrency)
            ? currency($pricePerNight, $baseCurrency, $userCurrency, false)
            : $pricePerNight;
        $convertedTotal = ($userCurrency !== $baseCurrency)
            ? currency($totalPrice, $baseCurrency, $userCurrency, false)
            : $totalPrice;

        $images = [];
        if ($space->featured_image_url) {
            $images[] = $space->featured_image_url;
        }
        $images = array_merge($images, $space->gallery_urls ?? []);

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
            images:                 $images,
            amenityNames:           $space->amenities->pluck('name')->all(),
            basePricePerNight:      $pricePerNight,
            baseCurrency:           $baseCurrency,
            convertedPricePerNight: (float) $convertedPrice,
            convertedCurrency:      $userCurrency,
            cleaningFee:            $cleaningFee,
            serviceFee:             $serviceFee,
            baseTotalPrice:         $totalPrice,
            convertedTotalPrice:    (float) $convertedTotal,
            isDiscounted:           $space->sale_price && $space->sale_price < $space->price_per_night,
        );
    }
}
