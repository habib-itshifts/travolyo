<?php

namespace Modules\Hotel\DTOs;

class ScrapedRoomDto
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $slug,
        public readonly string $roomType,
        public readonly string $currency,
        public readonly ?float $basePrice,
        public readonly int $maxAdults,
        public readonly int $maxChildren,
        public readonly int $maxOccupancy,
        public readonly ?float $sizeSqm,
        public readonly ?string $floor,
        public readonly ?string $viewType,
        public readonly ?string $description,
        public readonly array $bedConfiguration,
        public readonly array $amenityNames,
        public readonly int $quantity = 1,
        public readonly bool $isActive = true,
        public readonly int $sortOrder = 0,
    ) {}
}
