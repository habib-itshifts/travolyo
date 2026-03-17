<?php

namespace Modules\Hotel\DTOs;

class ScrapedHotelDto
{
    /**
     * @param  ScrapedRoomDto[]  $rooms
     */
    public function __construct(
        public readonly string $name,
        public readonly ?string $slug,
        public readonly ?int $starRating,
        public readonly ?string $shortDescription,
        public readonly ?string $description,
        public readonly ?string $featuredImageUrl,
        public readonly ?string $bannerImageUrl,
        public readonly array $galleryUrls,
        public readonly ?string $videoUrl,
        public readonly string $address,
        public readonly string $city,
        public readonly ?string $state,
        public readonly string $country,
        public readonly ?string $postalCode,
        public readonly ?float $latitude,
        public readonly ?float $longitude,
        public readonly ?string $email,
        public readonly ?string $phone,
        public readonly ?string $website,
        public readonly ?string $checkInTime,
        public readonly ?string $checkOutTime,
        public readonly ?float $basePrice,
        public readonly ?float $salePrice,
        public readonly ?int $minDayBeforeBooking,
        public readonly ?int $minDayStays,
        public readonly array $policies,
        public readonly array $nearbyPlaces,
        public readonly array $extraPrices,
        public readonly array $amenityNames,
        public readonly array $serviceNames,
        public readonly array $rooms,
        public readonly string $sourceUrl,
    ) {}
}
