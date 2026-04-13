<?php

namespace Modules\Space\DTOs;

class BookSpaceDto
{
    public function __construct(
        public readonly int     $spaceId,
        public readonly string  $checkIn,
        public readonly string  $checkOut,
        public readonly int     $guests,
        public readonly string  $currency = 'USD',
    ) {}
}
