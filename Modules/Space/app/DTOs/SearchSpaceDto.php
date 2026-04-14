<?php

namespace Modules\Space\DTOs;

use Carbon\Carbon;

class SearchSpaceDto
{
    public function __construct(
        public readonly string  $destination,
        public readonly string  $checkIn,
        public readonly string  $checkOut,
        public readonly int     $adults      = 1,
        public readonly int     $children    = 0,
        public readonly int     $infants     = 0,
        public readonly int     $guests      = 1,
        public readonly ?string $type        = null,   // apartment|room|studio|villa|house
        public readonly ?float  $priceMin    = null,
        public readonly ?float  $priceMax    = null,
        public readonly ?int    $bedrooms    = null,
        public readonly ?int    $bathrooms   = null,
        public readonly ?array  $amenities   = null,
        public readonly string  $currency    = 'USD',
        public readonly string  $sortBy      = 'price_asc',
        public readonly int     $perPage     = 20,
        public readonly int     $page        = 1,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            destination: $data['destination'] ?? $data['city'],
            checkIn:     $data['check_in'],
            checkOut:    $data['check_out'],
            adults:      (int) ($data['adults'] ?? $data['guests'] ?? 1),
            children:    (int) ($data['children'] ?? 0),
            infants:     (int) ($data['infants'] ?? 0),
            guests:      max(1, (int) ($data['adults'] ?? $data['guests'] ?? 1) + (int) ($data['children'] ?? 0) + (int) ($data['infants'] ?? 0)),
            type:        $data['type'] ?? null,
            priceMin:    isset($data['price_min']) ? (float) $data['price_min'] : null,
            priceMax:    isset($data['price_max']) ? (float) $data['price_max'] : null,
            bedrooms:    isset($data['bedrooms']) ? (int) $data['bedrooms'] : null,
            bathrooms:   isset($data['bathrooms']) ? (int) $data['bathrooms'] : null,
            amenities:   $data['amenities'] ?? null,
            currency:    $data['currency'] ?? 'USD',
            sortBy:      $data['sort_by'] ?? 'price_asc',
            perPage:     (int) ($data['per_page'] ?? 20),
            page:        (int) ($data['page'] ?? 1),
        );
    }

    public function nights(): int
    {
        return (int) Carbon::parse($this->checkIn)->diffInDays($this->checkOut);
    }
}
