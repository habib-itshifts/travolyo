<?php

namespace Modules\Flight\DTOs;

class FlightPassengerDto
{
    public function __construct(
        public readonly string  $type,        // adult | child | infant
        public readonly string  $firstName,
        public readonly string  $lastName,
        public readonly string  $dateOfBirth, // Y-m-d
        public readonly string  $gender,      // m | f
        public readonly string  $email,
        public readonly string  $phone,
        public readonly ?string $passportNumber   = null,
        public readonly ?string $passportExpiry   = null, // Y-m-d
        public readonly ?string $passportCountry  = null,
        public readonly ?string $nationality      = null,
    ) {}
}
