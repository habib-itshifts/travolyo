<?php

namespace Modules\Hotel\DTOs;

use Modules\Hotel\Enums\HotelProviderEnum;

class PrebookHotelDto
{
    public function __construct(
        public readonly string           $offerId,      // hotel id (DB or external)
        public readonly string           $roomId,       // room id (DB or external)
        public readonly HotelProviderEnum $provider,
        public readonly string           $checkIn,
        public readonly string           $checkOut,
        public readonly int              $adults,
        public readonly int              $children = 0,
        public readonly string           $currency  = 'USD',
    ) {}
}
