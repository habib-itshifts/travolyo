<?php

namespace Modules\Hotel\DTOs;

use Modules\Hotel\Enums\HotelProviderEnum;

class HotelOfferDto
{
    public function __construct(
        // Identity
        public readonly string            $offerId,         // DB id or external id
        public readonly HotelProviderEnum  $provider,

        // Basic info
        public readonly string            $name,
        public readonly int               $starRating,
        public readonly string            $city,
        public readonly string            $country,
        public readonly string            $address,
        public readonly ?string           $description,
        public readonly ?string           $shortDescription,

        // Policies
        public readonly ?string           $checkInTime,
        public readonly ?string           $checkOutTime,

        // Location
        public readonly ?float            $latitude,
        public readonly ?float            $longitude,

        // Media
        public readonly array             $images,          // array of URLs

        // Content
        public readonly array             $amenityNames,    // ["WiFi", "Pool", ...]
        public readonly array             $serviceNames,    // ["Room Service", ...]

        // Pricing (minimum across available rooms)
        public readonly float             $lowestPrice,
        public readonly string            $currency,
        public readonly string            $displayCurrency,

        // Rooms
        public readonly array             $rooms,           // HotelRoomOfferDto[]

        // Local provider only
        public readonly ?int              $dbHotelId = null,
        public readonly ?string           $slug      = null,

        // Badge
        public readonly ?string           $badge     = null, // 'best_value'|'popular'|null

        // B2B API source tag (e.g. 'local', 'netstorming_api', 'tasspro_api')
        public readonly ?string           $apiSource = null,
    ) {}
}
