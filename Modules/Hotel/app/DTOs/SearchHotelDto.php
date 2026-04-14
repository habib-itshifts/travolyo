<?php

namespace Modules\Hotel\DTOs;

use Carbon\Carbon;
use Modules\Hotel\Enums\HotelProviderEnum;

class SearchHotelDto
{
    public function __construct(
        public readonly string             $destination,
        public readonly string             $checkIn,
        public readonly string             $checkOut,
        public readonly int                $adults,
        public readonly int                $children   = 0,
        public readonly ?array             $childAges  = null,
        public readonly int                $rooms      = 1,
        public readonly ?int               $starRating = null,
        public readonly ?float             $priceMin   = null,
        public readonly ?float             $priceMax   = null,
        public readonly ?array             $amenities  = null,
        public readonly string             $currency     = 'USD',
        public readonly ?string            $nationality   = null,       
        public readonly string             $sortBy     = 'price_asc', // price_asc|price_desc|rating_desc|featured
        public readonly int                $perPage    = 20,
        public readonly int                $page       = 1,
        public readonly ?HotelProviderEnum $provider   = null,        // null = all providers
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            destination: $data['destination'] ?? $data['city'],
            checkIn:    $data['check_in'],
            checkOut:   $data['check_out'],
            adults:     (int) $data['adults'],
            children:   (int) ($data['children'] ?? 0),
             childAges:  isset($data['child_ages']) && is_array($data['child_ages'])
                ? array_values(array_map('intval', $data['child_ages']))
                : null,
            rooms:      (int) ($data['rooms'] ?? 1),
            starRating: isset($data['star_rating']) ? (int) $data['star_rating'] : null,
            priceMin:   isset($data['price_min']) ? (float) $data['price_min'] : null,
            priceMax:   isset($data['price_max']) ? (float) $data['price_max'] : null,
            amenities:  $data['amenities'] ?? null,
            currency:     $data['currency'] ?? 'USD',
            nationality: $data['nationality'] ?? config('hotel.default_nationality', 'AE'),
            sortBy:     $data['sort_by'] ?? 'price_asc',
            perPage:    (int) ($data['per_page'] ?? 20),
            page:       (int) ($data['page'] ?? 1),
            provider:   isset($data['provider']) ? HotelProviderEnum::from($data['provider']) : null
        );
    }

    public function nights(): int
    {
        return (int) Carbon::parse($this->checkIn)->diffInDays($this->checkOut);
    }

    public function guests(): int
    {
        return (int) ($this->adults + $this->children); 
    }
}
