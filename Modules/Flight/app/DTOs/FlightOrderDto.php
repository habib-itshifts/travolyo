<?php

namespace Modules\Flight\DTOs;

use Modules\Flight\Enums\FlightProviderEnum;
use Modules\Flight\Enums\FlightOrderStatusEnum;

class FlightOrderDto
{
    public function __construct(
        public readonly string            $orderId,
        public readonly FlightProviderEnum    $provider,
        public readonly FlightOrderStatusEnum $status,
        public readonly string            $origin,
        public readonly string            $destination,
        public readonly string            $departureAt,  // ISO 8601
        public readonly float             $totalAmount,
        public readonly string            $currency,
        public readonly array             $passengers,   // FlightPassengerDto[]
        public readonly array             $segments,
        public readonly ?string           $bookingReference = null,
        public readonly ?string           $ticketingDeadline = null,
    ) {}
}
