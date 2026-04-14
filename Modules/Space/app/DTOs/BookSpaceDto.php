<?php

namespace Modules\Space\DTOs;

class BookSpaceDto
{
    public function __construct(
        public readonly int     $spaceId,
        public readonly string  $checkIn,
        public readonly string  $checkOut,
        public readonly int     $adults,
        public readonly int     $children = 0,
        public readonly int     $infants = 0,
        public readonly int     $guests = 1,
        public readonly string  $currency = 'USD',
    ) {}
}
