<?php

namespace Modules\Hotel\DTOs;

class HotelRoomOfferDto
{
    public function __construct(
        public readonly string  $roomId,           // DB id or external id
        public readonly string  $name,
        public readonly string  $roomType,
        public readonly array   $bedConfiguration, // {"king":1}
        public readonly int     $maxAdults,
        public readonly int     $maxChildren,
        public readonly float   $basePrice,        // per night
        public readonly float   $totalPrice,       // for the full stay
        public readonly int     $nights,
        public readonly string  $currency,
        public readonly bool    $isAvailable,
        public readonly array   $amenityNames,     // ["AC", "TV", "Safe", ...]
        public readonly ?float  $sizeSqm   = null,
        public readonly ?string $viewType  = null,
        public readonly ?string $description = null,
        public readonly array   $images    = [],
    ) {}
}
