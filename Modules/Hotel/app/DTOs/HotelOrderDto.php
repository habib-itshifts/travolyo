<?php

namespace Modules\Hotel\DTOs;

use Modules\Hotel\Enums\HotelProviderEnum;

class HotelOrderDto
{
    public function __construct(
        public readonly string           $orderId,      // our booking code e.g. TRV-2026-ABC123
        public readonly HotelProviderEnum $provider,
        public readonly string           $hotelName,
        public readonly string           $roomName,
        public readonly string           $checkIn,
        public readonly string           $checkOut,
        public readonly int              $nights,
        public readonly int              $adults,
        public readonly int              $children,
        public readonly float            $totalPrice,
        public readonly string           $currency,
        public readonly string           $status,
        public readonly string           $guestFirstName,
        public readonly string           $guestLastName,
        public readonly string           $guestEmail,
        public readonly string           $guestPhone,
        public readonly ?string          $specialRequests = null,
    ) {}
}
