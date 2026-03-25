<?php

namespace Modules\Activity\Providers\Local;

use Modules\Activity\DTOs\ActivityOfferDto;
use Modules\Activity\Enums\ActivityProviderEnum;
use Modules\Activity\Models\Activity;

class LocalActivityMapper
{
    public function toOfferDto(Activity $activity): ActivityOfferDto
    {
        return new ActivityOfferDto(
            offerId:              (string) $activity->id,
            provider:             ActivityProviderEnum::Local,
            title:                $activity->title,
            slug:                 $activity->slug,
            category:             $activity->category,
            city:                 $activity->city,
            country:              $activity->country,
            address:              $activity->address,
            description:          $activity->description,
            pricePerPerson:       (float) ($activity->price_per_person ?: 0),
            currency:             strtoupper((string) ($activity->currency ?: 'AED')),
            maxParticipants:      $activity->max_participants,
            duration:             $activity->duration,
            instantConfirmation:  (bool) $activity->instant_confirmation,
            imageUrl:             $activity->image_url,
            galleryUrls:          $activity->gallery_urls,
            extraInformation:     is_array($activity->extra_information) ? $activity->extra_information : [],
            dbActivityId:         $activity->id,
            authorName:           $activity->author?->name,
        );
    }
}
