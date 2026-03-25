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
            'price_per_person'      => $this->pricePerPerson,
            'currency'              => $this->currency,

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
