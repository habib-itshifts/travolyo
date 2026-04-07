<?php

namespace Modules\Activity\Providers\Local;

use Modules\Activity\DTOs\ActivityOfferDto;
use Modules\Activity\Enums\ActivityProviderEnum;
use Modules\Activity\Models\Activity;

class LocalActivityMapper
{
    public function toOfferDto(Activity $activity, string $convertedCurrency = ''): ActivityOfferDto
    {
        $baseCurrency  = strtoupper((string) ($activity->currency ?: 'AED'));
        $basePrice     = (float) ($activity->price_per_person ?: 0);

        $convertedCurrency = strtoupper($convertedCurrency) ?: $baseCurrency;

        $convertedPrice = ($convertedCurrency === $baseCurrency)
            ? $basePrice
            : (float) currency($basePrice, $baseCurrency, $convertedCurrency, false);

        return new ActivityOfferDto(
            offerId:                 (string) $activity->id,
            provider:                ActivityProviderEnum::Local,
            title:                   $activity->title,
            slug:                    $activity->slug,
            category:                $activity->category,
            city:                    $activity->city,
            country:                 $activity->country,
            address:                 $activity->address,
            description:             $activity->description,
            basePricePerPerson:      $basePrice,
            baseCurrency:            $baseCurrency,
            convertedPricePerPerson: $convertedPrice,
            convertedCurrency:       $convertedCurrency,
            maxParticipants:         $activity->max_participants,
            duration:                $activity->duration,
            instantConfirmation:     (bool) $activity->instant_confirmation,
            imageUrl:                $activity->image_url,
            galleryUrls:             $activity->gallery_urls,
            extraInformation:        is_array($activity->extra_information) ? $activity->extra_information : [],
            dbActivityId:            $activity->id,
            authorName:              $activity->author?->name,
        );
    }
}
