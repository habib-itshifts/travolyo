<?php

namespace Modules\Activity\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Activity\DTOs\ActivityOfferDto;

/** @mixin ActivityOfferDto */
class ActivityOfferResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                    => $this->offerId,
            'provider'              => $this->provider->value,
            'db_activity_id'        => $this->dbActivityId,

            // Basic info
            'title'                 => $this->title,
            'slug'                  => $this->slug,
            'category'              => $this->category,
            'city'                  => $this->city,
            'country'               => $this->country,
            'address'               => $this->address,
            'description'           => $this->description,

            // Pricing
            'base_price_per_person'      => $this->basePricePerPerson,
            'base_currency'              => $this->baseCurrency,
            'converted_price_per_person' => $this->convertedPricePerPerson,
            'converted_currency'         => $this->convertedCurrency,

            // Capacity & duration
            'max_participants'      => $this->maxParticipants,
            'duration'              => $this->duration,
            'instant_confirmation'  => $this->instantConfirmation,

            // Media
            'image_url'             => $this->imageUrl,
            'gallery_urls'          => $this->galleryUrls,

            // Extra
            'extra_information'     => $this->extraInformation,

            // Author
            'author_name'           => $this->authorName,
        ];
    }
}
