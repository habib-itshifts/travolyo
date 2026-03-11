<?php

namespace Modules\Flight\DTOs;

use Modules\Flight\Enums\FlightProvider;
use Modules\Flight\Enums\FlightOrderStatus;

class FlightOrderDto
{
    public function __construct(
        public readonly string            $orderId,
        public readonly FlightProvider    $provider,
        public readonly FlightOrderStatus $status,
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
