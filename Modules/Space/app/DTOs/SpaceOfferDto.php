<?php

namespace Modules\Space\DTOs;

class SpaceOfferDto
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $name,
        public readonly string  $slug,
        public readonly string  $type,
        public readonly string  $city,
        public readonly string  $country,
        public readonly ?string $address,
        public readonly ?string $description,
        public readonly ?string $shortDescription,

        // Capacity
        public readonly int     $maxGuests,
        public readonly int     $bedrooms,
        public readonly int     $bathrooms,
        public readonly int     $beds,

        // Policies
        public readonly ?string $checkInTime,
        public readonly ?string $checkOutTime,

        // Location
        public readonly ?float  $latitude,
        public readonly ?float  $longitude,

        // Media
        public readonly array   $images,

        // Content
        public readonly array   $amenityNames,

        // Pricing
        public readonly float   $basePricePerNight,
        public readonly string  $baseCurrency,
        public readonly float   $convertedPricePerNight,
        public readonly string  $convertedCurrency,
        public readonly ?float  $cleaningFee,
        public readonly ?float  $serviceFee,

        // Total for requested stay
        public readonly ?float  $baseTotalPrice    = null,
        public readonly ?float  $convertedTotalPrice = null,

        public readonly bool    $isDiscounted      = false,
    ) {}
}
