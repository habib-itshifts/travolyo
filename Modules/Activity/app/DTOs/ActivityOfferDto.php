<?php

namespace Modules\Activity\DTOs;

use Modules\Activity\Enums\ActivityProviderEnum;

class ActivityOfferDto
{
    public function __construct(
        // Identity
        public readonly string               $offerId,       // DB id or external id
        public readonly ActivityProviderEnum  $provider,

        // Basic info
        public readonly string               $title,
        public readonly ?string              $slug,
        public readonly ?string              $category,
        public readonly ?string              $city,
        public readonly ?string              $country,
        public readonly ?string              $address,
        public readonly ?string              $description,

        // Pricing
        public readonly float                $basePricePerPerson,
        public readonly string               $baseCurrency,
        public readonly float                $convertedPricePerPerson,
        public readonly string               $convertedCurrency,

        // Capacity & duration
        public readonly ?int                 $maxParticipants,
        public readonly ?string              $duration,
        public readonly bool                 $instantConfirmation = true,

        // Media
        public readonly ?string              $imageUrl       = null,
        public readonly array                $galleryUrls    = [],

        // Extra
        public readonly array                $extraInformation = [],

        // Local provider only
        public readonly ?int                 $dbActivityId   = null,

        // Author info
        public readonly ?string              $authorName     = null,
    ) {}

    public function isDiscounted(): bool
    {
        return round($this->convertedPricePerPerson, 2) < round($this->basePricePerPerson, 2);
    }
}
