<?php

namespace Modules\Activity\DTOs;

use Modules\Activity\Enums\ActivityProviderEnum;

class SearchActivityDto
{
    public function __construct(
        public readonly string                 $destination,
        public readonly ?string                $category       = null,
        public readonly ?string                $activityDate   = null,
        public readonly int                    $participants   = 1,
        public readonly ?float                 $priceMin       = null,
        public readonly ?float                 $priceMax       = null,
        public readonly ?bool                  $instantConfirmation = null,
        public readonly string                 $currency       = 'AED',
        public readonly string                 $sortBy         = 'recommended', // recommended|price_asc|price_desc
        public readonly int                    $perPage        = 20,
        public readonly int                    $page           = 1,
        public readonly ?ActivityProviderEnum  $provider       = null, // null = all providers
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            destination:          $data['destination'] ?? $data['city'] ?? '',
            category:             $data['category'] ?? null,
            activityDate:         $data['activity_date'] ?? null,
            participants:         (int) ($data['participants'] ?? 1),
            priceMin:             isset($data['price_min']) ? (float) $data['price_min'] : null,
            priceMax:             isset($data['price_max']) ? (float) $data['price_max'] : null,
            instantConfirmation:  isset($data['instant_confirmation']) ? (bool) $data['instant_confirmation'] : null,
            currency:             $data['currency'] ?? 'AED',
            sortBy:               $data['sort_by'] ?? 'recommended',
            perPage:              (int) ($data['per_page'] ?? 20),
            page:                 (int) ($data['page'] ?? 1),
            provider:             isset($data['provider']) ? ActivityProviderEnum::from($data['provider']) : null,
        );
    }
}
