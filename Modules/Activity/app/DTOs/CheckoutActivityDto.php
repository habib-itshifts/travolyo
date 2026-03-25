<?php

namespace Modules\Activity\DTOs;

class CheckoutActivityDto
{
    public function __construct(
        public readonly string  $checkoutToken,      // cache key from prebook step
        public readonly string  $contactEmail,
        public readonly string  $contactPhone,
        public readonly string  $paymentGateway,
        public readonly array   $passengers,         // array of passenger arrays
        public readonly ?string $specialRequests = null,
        public readonly ?int    $customerId      = null,
    ) {}
}
