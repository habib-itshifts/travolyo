<?php

namespace Modules\Space\DTOs;

class SpaceOrderDto
{
    public function __construct(
        public readonly string  $orderId,
        public readonly string  $spaceName,
        public readonly string  $spaceType,
        public readonly string  $city,
        public readonly string  $country,
        public readonly ?string $address,
        public readonly string  $checkIn,
        public readonly string  $checkOut,
        public readonly int     $nights,
        public readonly int     $guests,
        public readonly float   $pricePerNight,
        public readonly float   $cleaningFee,
        public readonly float   $serviceFee,
        public readonly float   $totalPrice,
        public readonly string  $currency,
        public readonly string  $status,
        public readonly ?string $guestName,
        public readonly ?string $guestEmail,
        public readonly ?string $specialRequests,
        public readonly array   $images = [],
    ) {}
}
