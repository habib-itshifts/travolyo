<?php

namespace Modules\Space\DTOs;

class CheckoutSpaceDto
{
    public function __construct(
        public readonly string  $checkoutToken,
        public readonly string  $firstName,
        public readonly string  $lastName,
        public readonly string  $email,
        public readonly string  $phone,
        public readonly string  $paymentGateway,
        public readonly ?string $specialRequests = null,
        public readonly array   $extraServices   = [],
        public readonly ?int    $customerId      = null,
    ) {}
}
