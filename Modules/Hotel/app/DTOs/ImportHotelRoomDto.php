<?php

namespace Modules\Hotel\DTOs;

class ImportHotelRoomDto
{
    public function __construct(
        public readonly int $hotelId,
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
        public readonly array $amenityIds,
        public readonly int $quantity = 1,
        public readonly bool $isActive = true,
        public readonly int $sortOrder = 0,
    ) {}

    public function toArray(): array
    {
        return [
            'hotel_id' => $this->hotelId,
            'name' => $this->name,
            'slug' => $this->slug,
            'room_type' => $this->roomType,
            'currency' => $this->currency,
            'bed_configuration_text' => implode(', ', $this->bedConfiguration),
            'max_adults' => $this->maxAdults,
            'max_children' => $this->maxChildren,
            'max_occupancy' => $this->maxOccupancy,
            'size_sqm' => $this->sizeSqm,
            'floor' => $this->floor,
            'view_type' => $this->viewType,
            'description' => $this->description,
            'base_price' => $this->basePrice,
            'quantity' => $this->quantity,
            'is_active' => $this->isActive,
            'sort_order' => $this->sortOrder,
            'amenity_ids' => $this->amenityIds,
        ];
    }
}
