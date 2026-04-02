<?php

namespace Modules\Hotel\Providers\TravolyoB2B;

use Carbon\Carbon;
use Modules\Hotel\DTOs\HotelOfferDto;
use Modules\Hotel\DTOs\HotelRoomOfferDto;
use Modules\Hotel\DTOs\PrebookHotelDto;
use Modules\Hotel\Enums\HotelProviderEnum;

class TravolyoB2BHotelMapper
{
    public function toOfferDto(array $hotel, int $nights, string $currency): HotelOfferDto
    {
        $baseCurrency = (string) ($hotel['currency'] ?? $currency);
        $baseLowestPrice = (float) ($hotel['min_price'] ?? $hotel['lowest_price'] ?? $hotel['price'] ?? 0);
        $convertedLowestPrice = $this->convertAmount($baseLowestPrice, $baseCurrency, $currency);

        return new HotelOfferDto(
            offerId: $this->resolveOfferId($hotel),
            provider: HotelProviderEnum::TravolyoB2B,
            name: (string) ($hotel['name'] ?? $hotel['hotel_name'] ?? $hotel['hotel_code'] ?? 'B2B Hotel'),
            starRating: (int) ($hotel['star_rating'] ?? 0),
            city: (string) ($hotel['city'] ?? $hotel['hotel_city'] ?? ''),
            country: (string) ($hotel['country'] ?? $hotel['hotel_country'] ?? ''),
            address: (string) ($hotel['address'] ?? ''),
            description: $this->nullableString($hotel['description'] ?? null),
            shortDescription: $this->nullableString($hotel['short_description'] ?? null),
            checkInTime: $this->nullableString($hotel['check_in_time'] ?? null),
            checkOutTime: $this->nullableString($hotel['check_out_time'] ?? null),
            latitude: $this->floatOrNull($hotel['latitude'] ?? null),
            longitude: $this->floatOrNull($hotel['longitude'] ?? null),
            images: $this->normalizeImages($hotel),
            amenityNames: $this->parseFacilities($hotel['facilities'] ?? $hotel['amenities'] ?? []),
            serviceNames: [],
            baseLowestPrice: $baseLowestPrice,
            baseCurrency: $baseCurrency,
            convertedLowestPrice: $convertedLowestPrice,
            convertedCurrency: $currency,
            rooms: [],
            badge: null,
            apiSource: $this->nullableString($hotel['source'] ?? $hotel['api_source'] ?? null),
        );
    }

    public function toRoomOfferDto(array $room, int $nights, string $currency, array $context = []): HotelRoomOfferDto
    {
        $nights = max($nights, 1);
        $baseCurrency = (string) ($room['currency'] ?? $context['currency'] ?? $currency);

        $baseTotalPrice = (float) ($room['total_price'] ?? $room['agreement_price'] ?? 0);
        $baseCurrentPrice = (float) ($room['price_per_night'] ?? 0);

        if ($baseCurrentPrice <= 0 && $baseTotalPrice > 0) {
            $baseCurrentPrice = round($baseTotalPrice / $nights, 2);
        }

        if ($baseTotalPrice <= 0 && $baseCurrentPrice > 0) {
            $baseTotalPrice = $baseCurrentPrice * $nights;
        }

        $baseOriginalPrice = (float) ($room['original_price'] ?? $room['rack_rate'] ?? 0);
        if ($baseOriginalPrice <= $baseCurrentPrice) {
            $baseOriginalPrice = 0.0;
        }

        $convertedCurrentPrice = $this->convertAmount($baseCurrentPrice, $baseCurrency, $currency);
        $convertedTotalPrice = $this->convertAmount($baseTotalPrice, $baseCurrency, $currency);
        $convertedOriginalPrice = $baseOriginalPrice > 0
            ? $this->convertAmount($baseOriginalPrice, $baseCurrency, $currency)
            : 0.0;

        $amenities = $this->parseFacilities($room['amenities'] ?? []);
        $mealBasis = trim((string) ($room['meal_basis_name'] ?? ''));
        if ($mealBasis !== '') {
            $amenities[] = $mealBasis;
        }
        $amenities = array_values(array_unique(array_filter($amenities)));

        $bedConfiguration = $room['bed_configuration'] ?? [];
        if (is_string($bedConfiguration)) {
            $bedConfiguration = array_filter([trim($bedConfiguration)]);
        } elseif (! is_array($bedConfiguration)) {
            $bedConfiguration = [];
        }

        return new HotelRoomOfferDto(
            roomId: $this->encodeBookingKey([
                'hotel_code' => (string) ($context['hotel_code'] ?? $context['offer_id'] ?? ''),
                'hotel_name' => (string) ($context['hotel_name'] ?? ''),
                'hotel_city' => (string) ($context['hotel_city'] ?? ''),
                'hotel_country' => (string) ($context['hotel_country'] ?? ''),
                'city_code' => (string) ($context['city_code'] ?? $this->deriveCityCode((string) ($context['offer_id'] ?? $context['hotel_code'] ?? ''))),
                'source' => (string) ($context['api_source'] ?? $room['source'] ?? ''),
                'agreement_code' => (string) ($room['agreement_code'] ?? ''),
                'token_id' => (string) ($room['token_id'] ?? ''),
                'rate_key' => (string) ($room['rate_key'] ?? ''),
                'search_number' => (string) ($room['search_number'] ?? ''),
                'agreement_price' => (string) ($room['agreement_price'] ?? $room['total_price'] ?? ''),
                'room_type_code' => (string) ($room['room_type_code'] ?? 'dbl'),
                'room_type_name' => (string) ($room['room_type_name'] ?? $room['name'] ?? 'Room'),
                'meal_basis_code' => (string) ($room['meal_basis_code'] ?? ''),
                'meal_basis_name' => (string) ($room['meal_basis_name'] ?? ''),
                'occupancy' => (int) ($room['occupancy'] ?? $room['adults'] ?? $context['adults'] ?? 2),
                'total_price' => $baseTotalPrice,
                'currency' => $baseCurrency,
            ]),
            name: (string) ($room['room_type_name'] ?? $room['name'] ?? 'Room'),
            roomType: (string) ($room['room_type_code'] ?? $room['room_type_name'] ?? 'standard'),
            bedConfiguration: $bedConfiguration,
            maxAdults: (int) ($room['adults'] ?? $room['occupancy'] ?? $context['adults'] ?? 2),
            maxChildren: (int) ($room['children'] ?? $context['children'] ?? 0),
            baseOriginalPrice: round($baseOriginalPrice, 2),
            convertedOriginalPrice:round($convertedOriginalPrice, 2),
            baseCurrentPrice: round($baseCurrentPrice,2),
            convertedCurrentPrice: round($convertedCurrentPrice, 2),
            baseTotalPrice: round($baseTotalPrice, 2),
            convertedTotalPrice: round($convertedTotalPrice, 2),
            nights: $nights,
            baseCurrency: $baseCurrency,
            convertedCurrency: $currency,
            isAvailable: (bool) ($room['is_available'] ?? $room['available'] ?? true),
            amenityNames: $amenities,
            sizeSqm: $this->floatOrNull($room['size_sqm'] ?? null),
            viewType: $this->nullableString($room['view_type'] ?? null),
            description: $this->nullableString($room['description'] ?? null),
            images: $this->normalizeImages($room),
            dealId: null,
        );
    }

    public function toPrebookOfferDto(array $prebook, array $roomKeys, PrebookHotelDto $dto): HotelOfferDto
    {
        $nights = max((int) Carbon::parse($dto->checkIn)->diffInDays($dto->checkOut), 1);

        $room = $this->toRoomOfferDto(
            room: array_merge($roomKeys, $prebook),
            nights: $nights,
            currency: $dto->currency,
            context: [
                'offer_id' => $dto->offerId,
                'hotel_code' => $roomKeys['hotel_code'] ?? $dto->offerId,
                'hotel_name' => $prebook['hotel_name'] ?? $roomKeys['hotel_name'] ?? $dto->offerId,
                'hotel_city' => $prebook['hotel_city'] ?? $roomKeys['hotel_city'] ?? '',
                'hotel_country' => $prebook['hotel_country'] ?? $roomKeys['hotel_country'] ?? '',
                'city_code' => $prebook['city_code'] ?? $roomKeys['city_code'] ?? $this->deriveCityCode($dto->offerId),
                'currency' => $prebook['currency'] ?? $roomKeys['currency'] ?? $dto->currency,
                'api_source' => $roomKeys['source'] ?? $prebook['source'] ?? null,
            ],
        );

        return new HotelOfferDto(
            offerId: $dto->offerId,
            provider: HotelProviderEnum::TravolyoB2B,
            name: (string) ($prebook['hotel_name'] ?? $roomKeys['hotel_name'] ?? $dto->offerId),
            starRating: 0,
            city: (string) ($prebook['hotel_city'] ?? $roomKeys['hotel_city'] ?? ''),
            country: (string) ($prebook['hotel_country'] ?? $roomKeys['hotel_country'] ?? ''),
            address: '',
            description: null,
            shortDescription: null,
            checkInTime: null,
            checkOutTime: null,
            latitude: null,
            longitude: null,
            images: [],
            amenityNames: [],
            serviceNames: [],
            baseLowestPrice: $room->baseCurrentPrice,
            baseCurrency: $room->baseCurrency,
            convertedLowestPrice: $room->convertedCurrentPrice,
            convertedCurrency: $room->convertedCurrency,
            rooms: [$room],
            badge: null,
            apiSource: $this->nullableString($roomKeys['source'] ?? $prebook['source'] ?? null),
        );
    }

    public function encodeBookingKey(array $keys): string
    {
        return base64_encode(json_encode($keys));
    }

    public function decodeBookingKey(string $roomId): array
    {
        $decoded = base64_decode($roomId, true);

        if ($decoded === false) {
            $decoded = $roomId;
        }

        $payload = json_decode($decoded, true);

        if (! is_array($payload)) {
            throw new \InvalidArgumentException("Invalid Travolyo B2B room key: [{$roomId}]");
        }

        return $payload;
    }

    public function parseFacilities(array|string|null $raw): array
    {
        if (is_string($raw)) {
            return array_values(array_filter(array_map('trim', explode(',', $raw))));
        }

        if (! is_array($raw)) {
            return [];
        }

        $flat = [];

        foreach ($raw as $item) {
            if (is_string($item)) {
                foreach (explode(',', $item) as $part) {
                    $part = trim($part);
                    if ($part !== '') {
                        $flat[] = $part;
                    }
                }
            } elseif (is_array($item)) {
                $label = $item['name'] ?? $item['label'] ?? null;
                if (is_string($label) && trim($label) !== '') {
                    $flat[] = trim($label);
                }
            }
        }

        return array_values(array_unique($flat));
    }

    private function resolveOfferId(array $hotel): string
    {
        return (string) ($hotel['hotel_code'] ?? $hotel['id'] ?? uniqid('b2b_', true));
    }

    private function normalizeImages(array $payload): array
    {
        $images = [];

        foreach (['image_url', 'thumbnail', 'featured_image'] as $key) {
            if (! empty($payload[$key]) && is_string($payload[$key])) {
                $images[] = $payload[$key];
            }
        }

        if (! empty($payload['images']) && is_array($payload['images'])) {
            foreach ($payload['images'] as $image) {
                if (is_string($image) && trim($image) !== '') {
                    $images[] = trim($image);
                } elseif (is_array($image)) {
                    $url = $image['url'] ?? $image['image_url'] ?? null;
                    if (is_string($url) && trim($url) !== '') {
                        $images[] = trim($url);
                    }
                }
            }
        }

        return array_values(array_unique(array_filter($images)));
    }

    private function convertAmount(float $amount, string $fromCurrency, string $toCurrency): float
    {
        if ($amount <= 0 || $fromCurrency === '' || $toCurrency === '' || strtoupper($fromCurrency) === strtoupper($toCurrency)) {
            return (float) $amount;
        }

        try {
            return (float) currency($amount, $fromCurrency, $toCurrency, false);
        } catch (\Throwable) {
            return (float) $amount;
        }
    }

    private function floatOrNull(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (float) $value;
    }

    private function nullableString(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    private function deriveCityCode(string $offerId): string
    {
        if (preg_match('/^([A-Za-z]{3})/', $offerId, $matches)) {
            return strtoupper($matches[1]);
        }

        return '';
    }
}
