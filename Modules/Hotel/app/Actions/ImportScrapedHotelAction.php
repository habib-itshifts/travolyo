<?php

namespace Modules\Hotel\Actions;

use App\Models\Currency;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Modules\Hotel\DTOs\ImportHotelDto;
use Modules\Hotel\DTOs\ImportHotelRoomDto;
use Modules\Hotel\DTOs\ScrapeHotelRequestDto;
use Modules\Hotel\DTOs\ScrapedHotelDto;
use Modules\Hotel\DTOs\ScrapedRoomDto;
use Modules\Hotel\Models\Amenity;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelRoom;
use Modules\Hotel\Models\Service;
use RuntimeException;

class ImportScrapedHotelAction
{
    public function __construct(
        private readonly SaveHotelAction $saveHotel,
        private readonly SaveHotelRoomAction $saveRoom,
    ) {}

    public function execute(ScrapeHotelRequestDto $requestDto): Hotel
    {
        $scrapedHotel = $this->scrapeHotel($requestDto->url);

        return DB::transaction(function () use ($requestDto, $scrapedHotel) {
            $existingHotel = $this->findExistingHotel($scrapedHotel, $requestDto);
            $shouldReplaceRooms = $existingHotel !== null;

            $hotelImportDto = new ImportHotelDto(
                name: $scrapedHotel->name,
                slug: $existingHotel?->slug ?? $this->makeUniqueHotelSlug($scrapedHotel->slug ?: $scrapedHotel->name, $existingHotel),
                scrapingUrl: $scrapedHotel->sourceUrl,
                starRating: $scrapedHotel->starRating,
                shortDescription: $scrapedHotel->shortDescription,
                description: $scrapedHotel->description,
                featuredImageUrl: $scrapedHotel->featuredImageUrl,
                bannerImageUrl: $scrapedHotel->bannerImageUrl,
                galleryUrls: $scrapedHotel->galleryUrls,
                videoUrl: $scrapedHotel->videoUrl,
                address: $scrapedHotel->address,
                city: $scrapedHotel->city,
                state: $scrapedHotel->state,
                country: $scrapedHotel->country,
                postalCode: $scrapedHotel->postalCode,
                latitude: $scrapedHotel->latitude,
                longitude: $scrapedHotel->longitude,
                email: $scrapedHotel->email,
                phone: $scrapedHotel->phone,
                website: $scrapedHotel->website,
                checkInTime: $scrapedHotel->checkInTime,
                checkOutTime: $scrapedHotel->checkOutTime,
                basePrice: $scrapedHotel->basePrice,
                salePrice: $scrapedHotel->salePrice,
                minDayBeforeBooking: $scrapedHotel->minDayBeforeBooking,
                minDayStays: $scrapedHotel->minDayStays,
                policies: $scrapedHotel->policies,
                nearbyPlaces: $scrapedHotel->nearbyPlaces,
                extraPrices: $scrapedHotel->extraPrices,
                amenityIds: $this->resolveAmenityIds($scrapedHotel->amenityNames, 'hotel'),
                serviceIds: $this->resolveServiceIds($scrapedHotel->serviceNames),
                status: $requestDto->isVendor ? null : ($requestDto->status ?? ($existingHotel?->status ?? 'draft')),
                isFeatured: $requestDto->isVendor ? false : ($existingHotel?->is_featured ?? false),
                sortOrder: $existingHotel?->sort_order ?? 0,
            );

            $hotel = $this->saveHotel->execute(
                $hotelImportDto->toArray(),
                isVendor: $requestDto->isVendor,
                hotel: $existingHotel,
            );

            if ($shouldReplaceRooms) {
                $hotel->rooms()->delete();
            }

            $this->importRooms($hotel, $scrapedHotel, replaceExisting: $shouldReplaceRooms);

            return $hotel->fresh(['rooms', 'amenities', 'services']) ?? $hotel;
        });
    }

    private function scrapeHotel(string $url): ScrapedHotelDto
    {
        $endpoint = trim((string) config('services.hotel_scraping.endpoint'));
        $timeout = max(30, (int) config('services.hotel_scraping.timeout', 180));

        if ($endpoint === '') {
            throw new RuntimeException('Hotel scraping endpoint is not configured.');
        }

        $this->extendExecutionTime($timeout + 10);

        try {
            $response = Http::connectTimeout(min(30, $timeout))
                ->timeout($timeout)
                ->acceptJson()
                ->post($endpoint, ['url' => $url]);
        } catch (ConnectionException $exception) {
            throw new RuntimeException('Scraping service timed out or is unreachable. Please try again in a moment.', previous: $exception);
        } catch (RequestException $exception) {
            throw new RuntimeException('Scraping service request failed. Please try again in a moment.', previous: $exception);
        }

        if ($response->failed()) {
            throw new RuntimeException('Hotel scraping request failed with status ' . $response->status() . '.');
        }

        $payload = $response->json();
        if (! is_array($payload)) {
            throw new RuntimeException('Hotel scraping response was not valid JSON.');
        }

        return $this->toScrapedHotelDto($this->extractHotelPayload($payload), $url);
    }

    private function extractHotelPayload(array $payload): array
    {
        $candidate = $payload;

        for ($i = 0; $i < 4; $i++) {
            $changed = false;

            foreach (['data', 'result', 'payload', 'hotel'] as $key) {
                if (isset($candidate[$key]) && is_array($candidate[$key])) {
                    $candidate = $candidate[$key];
                    $changed = true;
                    break;
                }
            }

            if (! $changed) {
                break;
            }
        }

        if (array_is_list($candidate) && isset($candidate[0]) && is_array($candidate[0])) {
            $candidate = $candidate[0];
        }

        if (! is_array($candidate) || $candidate === []) {
            throw new RuntimeException('Hotel scraping response did not contain hotel data.');
        }

        return $candidate;
    }

    private function toScrapedHotelDto(array $payload, string $sourceUrl): ScrapedHotelDto
    {
        $normalizedSourceUrl = $this->normalizeSourceUrl($sourceUrl);
        $galleryUrls = $this->normalizeUrlList(
            $this->firstValue($payload, ['gallery_urls', 'gallery', 'images', 'photos', 'hotel_images'])
        );
        $featuredImageUrl = $this->normalizeUrl($this->firstValue($payload, ['featured_image_url', 'main_image', 'image', 'thumbnail']))
            ?? ($galleryUrls[0] ?? null);
        $bannerImageUrl = $this->normalizeUrl($this->firstValue($payload, ['banner_image_url', 'hero_image', 'cover_image']))
            ?? ($galleryUrls[1] ?? $featuredImageUrl);

        $description = $this->normalizeLongText(
            $this->firstValue($payload, ['description', 'hotel_description', 'about', 'summary'])
        );

        $rooms = $this->extractRoomsPayload($payload)
            ->map(fn (array $room, int $index) => $this->toScrapedRoomDto(
                $room,
                $this->normalizeCurrency($this->firstValue($payload, ['currency', 'price_currency'])),
                $index
            ))
            ->filter(fn (ScrapedRoomDto $room) => $room->name !== '')
            ->values()
            ->all();

        $lowestRoomPrice = collect($rooms)
            ->pluck('basePrice')
            ->filter(fn ($price) => $price !== null)
            ->min();

        $name = $this->normalizeText($this->firstValue($payload, ['name', 'hotel_name', 'title']));
        if ($name === null) {
            throw new RuntimeException('Hotel scraping response did not include a hotel name.');
        }

        $city = $this->normalizeText($this->firstValue($payload, ['city', 'location.city', 'address.city']))
            ?? (string) config('services.hotel_scraping.fallback_city', 'Unknown');

        $country = $this->normalizeCountryCode(
            $this->normalizeText($this->firstValue($payload, ['country_code', 'country', 'location.country_code', 'location.country']))
        );

        $address = $this->normalizeText($this->firstValue($payload, ['address', 'hotel_address', 'location.address']))
            ?? trim($city . ', ' . $country);

        $amenityNames = $this->normalizeNameList(
            $this->firstValue($payload, ['amenities', 'facilities', 'hotel_facilities', 'popular_facilities'])
        );
        $serviceNames = $this->normalizeNameList(
            $this->firstValue($payload, ['services', 'hotel_services', 'property_services'])
        );

        return new ScrapedHotelDto(
            name: $name,
            slug: $this->normalizeSlug($this->firstValue($payload, ['slug', 'hotel_slug'])),
            starRating: $this->normalizeStarRating($this->firstValue($payload, ['star_rating', 'stars', 'hotel_rating', 'rating'])),
            shortDescription: $this->normalizeShortText(
                $this->firstValue($payload, ['short_description', 'summary', 'tagline'])
            ) ?? $this->fallbackShortDescription($description),
            description: $description,
            featuredImageUrl: $featuredImageUrl,
            bannerImageUrl: $bannerImageUrl,
            galleryUrls: $galleryUrls,
            videoUrl: $this->normalizeUrl($this->firstValue($payload, ['video_url', 'video'])),
            address: $address,
            city: $city,
            state: $this->normalizeText($this->firstValue($payload, ['state', 'region', 'location.state'])),
            country: $country,
            postalCode: $this->normalizeText($this->firstValue($payload, ['postal_code', 'zip', 'zipcode'])),
            latitude: $this->normalizeFloat($this->firstValue($payload, ['latitude', 'lat', 'location.latitude'])),
            longitude: $this->normalizeFloat($this->firstValue($payload, ['longitude', 'lng', 'lon', 'location.longitude'])),
            email: $this->normalizeEmail($this->firstValue($payload, ['email', 'contact.email'])),
            phone: $this->normalizeText($this->firstValue($payload, ['phone', 'telephone', 'contact.phone'])),
            website: $this->normalizeUrl($this->firstValue($payload, ['website', 'contact.website'])),
            checkInTime: $this->normalizeTime($this->firstValue($payload, ['check_in_time', 'checkin', 'check_in'])) ?? '14:00',
            checkOutTime: $this->normalizeTime($this->firstValue($payload, ['check_out_time', 'checkout', 'check_out'])) ?? '11:00',
            basePrice: $this->normalizeMoney($this->firstValue($payload, ['base_price', 'price', 'hotel_price'])) ?? $lowestRoomPrice,
            salePrice: $this->normalizeMoney($this->firstValue($payload, ['sale_price', 'discounted_price'])),
            minDayBeforeBooking: $this->normalizeInt($this->firstValue($payload, ['min_day_before_booking'])),
            minDayStays: $this->normalizeInt($this->firstValue($payload, ['min_day_stays', 'minimum_stay'])),
            policies: $this->normalizePolicies($this->firstValue($payload, ['policies', 'hotel_policies', 'house_rules', 'cancellation_policy'])),
            nearbyPlaces: $this->normalizeNearbyPlaces($this->firstValue($payload, ['nearby_places', 'surroundings', 'points_of_interest'])),
            extraPrices: $this->normalizeExtraPrices($this->firstValue($payload, ['extra_prices', 'fees'])),
            amenityNames: $amenityNames,
            serviceNames: $serviceNames,
            rooms: $rooms,
            sourceUrl: $normalizedSourceUrl,
        );
    }

    private function importRooms(Hotel $hotel, ScrapedHotelDto $scrapedHotel, bool $replaceExisting = false): void
    {
        foreach ($scrapedHotel->rooms as $scrapedRoom) {
            $existingRoom = $replaceExisting ? null : $this->findExistingRoom($hotel, $scrapedRoom);

            $roomImportDto = new ImportHotelRoomDto(
                hotelId: $hotel->id,
                name: $scrapedRoom->name,
                slug: $existingRoom?->slug ?? $this->makeUniqueRoomSlug($hotel, $scrapedRoom->slug ?: $scrapedRoom->name, $existingRoom),
                roomType: $scrapedRoom->roomType,
                currency: $scrapedRoom->currency,
                basePrice: $scrapedRoom->basePrice ?? ($hotel->base_price ? (float) $hotel->base_price : 0.0),
                maxAdults: $scrapedRoom->maxAdults,
                maxChildren: $scrapedRoom->maxChildren,
                maxOccupancy: $scrapedRoom->maxOccupancy,
                sizeSqm: $scrapedRoom->sizeSqm,
                floor: $scrapedRoom->floor,
                viewType: $scrapedRoom->viewType,
                description: $scrapedRoom->description,
                bedConfiguration: $scrapedRoom->bedConfiguration,
                amenityIds: $this->resolveAmenityIds($scrapedRoom->amenityNames, 'room'),
                quantity: max(1, $scrapedRoom->quantity),
                isActive: $scrapedRoom->isActive,
                sortOrder: $scrapedRoom->sortOrder,
            );

            $this->saveRoom->execute($roomImportDto->toArray(), $existingRoom);
        }
    }

    private function findExistingHotel(ScrapedHotelDto $scrapedHotel, ScrapeHotelRequestDto $requestDto): ?Hotel
    {
        $query = Hotel::query();

        if ($requestDto->actorId > 0) {
            $query->where('author_id', $requestDto->actorId);
        }

        if ($scrapedHotel->sourceUrl !== '') {
            $hotel = (clone $query)
                ->where('scraping_url', $scrapedHotel->sourceUrl)
                ->first();
            if ($hotel) {
                return $hotel;
            }
        }

        $slug = $this->normalizeSlug($scrapedHotel->slug ?: $scrapedHotel->name);
        if ($slug) {
            $hotel = (clone $query)->where('slug', $slug)->first();
            if ($hotel) {
                return $hotel;
            }
        }

        return (clone $query)
            ->whereRaw('LOWER(name) = ?', [Str::lower($scrapedHotel->name)])
            ->whereRaw('LOWER(address) = ?', [Str::lower($scrapedHotel->address)])
            ->first();
    }

    private function findExistingRoom(Hotel $hotel, ScrapedRoomDto $scrapedRoom): ?HotelRoom
    {
        $slug = $this->normalizeSlug($scrapedRoom->slug ?: $scrapedRoom->name);
        if ($slug) {
            $room = $hotel->rooms()->where('slug', $slug)->first();
            if ($room) {
                return $room;
            }
        }

        return $hotel->rooms()
            ->whereRaw('LOWER(name) = ?', [Str::lower($scrapedRoom->name)])
            ->first();
    }

    private function resolveAmenityIds(array $names, string $scope): array
    {
        return collect($names)
            ->map(fn ($name) => $this->normalizeText($name))
            ->filter()
            ->unique()
            ->map(fn (string $name) => $this->findOrCreateAmenity($name, $scope)->id)
            ->values()
            ->all();
    }

    private function resolveServiceIds(array $names): array
    {
        return collect($names)
            ->map(fn ($name) => $this->normalizeText($name))
            ->filter()
            ->unique()
            ->map(fn (string $name) => $this->findOrCreateService($name)->id)
            ->values()
            ->all();
    }

    private function findOrCreateAmenity(string $name, string $scope): Amenity
    {
        $amenity = Amenity::query()
            ->whereRaw('LOWER(name) = ?', [Str::lower($name)])
            ->first();

        if ($amenity) {
            $target = $scope;
            if ($amenity->applies_to !== $target && $amenity->applies_to !== 'both') {
                $amenity->update(['applies_to' => 'both']);
            }

            if (! $amenity->is_active) {
                $amenity->update(['is_active' => true]);
            }

            return $amenity;
        }

        return Amenity::query()->create([
            'name' => $name,
            'slug' => $this->makeUniqueAmenitySlug($name),
            'category' => $this->inferAmenityCategory($name),
            'applies_to' => $scope,
            'is_active' => true,
            'sort_order' => 0,
        ]);
    }

    private function findOrCreateService(string $name): Service
    {
        $service = Service::query()
            ->whereRaw('LOWER(name) = ?', [Str::lower($name)])
            ->first();

        if ($service) {
            if (! $service->is_active) {
                $service->update(['is_active' => true]);
            }

            return $service;
        }

        return Service::query()->create([
            'name' => $name,
            'slug' => $this->makeUniqueServiceSlug($name),
            'category' => $this->inferServiceCategory($name),
            'is_chargeable' => false,
            'is_active' => true,
            'sort_order' => 0,
        ]);
    }

    private function makeUniqueHotelSlug(string $value, ?Hotel $ignore = null): string
    {
        return $this->makeUniqueSlug($value, Hotel::query(), $ignore?->id);
    }

    private function makeUniqueRoomSlug(Hotel $hotel, string $value, ?HotelRoom $ignore = null): string
    {
        return $this->makeUniqueSlug(
            $value,
            HotelRoom::query()->where('hotel_id', $hotel->id),
            $ignore?->id
        );
    }

    private function makeUniqueAmenitySlug(string $value): string
    {
        return $this->makeUniqueSlug($value, Amenity::query());
    }

    private function makeUniqueServiceSlug(string $value): string
    {
        return $this->makeUniqueSlug($value, Service::query());
    }

    private function makeUniqueSlug(string $value, $query, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: 'imported-item';
        $slug = $base;
        $suffix = 2;

        while ($this->slugExists($query, $slug, $ignoreId)) {
            $slug = $base . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }

    private function slugExists($query, string $slug, ?int $ignoreId = null): bool
    {
        $query = clone $query;

        if ($ignoreId) {
            $query->whereKeyNot($ignoreId);
        }

        return $query->where('slug', $slug)->exists();
    }

    private function toScrapedRoomDto(array $room, string $fallbackCurrency, int $index): ScrapedRoomDto
    {
        $name = $this->normalizeText($this->firstValue($room, ['name', 'title', 'room_name']))
            ?? 'Room ' . ($index + 1);

        $maxAdults = max(1, $this->normalizeInt($this->firstValue($room, ['max_adults', 'adults', 'guests'])) ?? 2);
        $maxChildren = max(0, $this->normalizeInt($this->firstValue($room, ['max_children', 'children'])) ?? 0);
        $maxOccupancy = max($maxAdults + $maxChildren, $this->normalizeInt($this->firstValue($room, ['max_occupancy', 'occupancy'])) ?? $maxAdults);

        return new ScrapedRoomDto(
            name: $name,
            slug: $this->normalizeSlug($this->firstValue($room, ['slug', 'room_slug'])),
            roomType: $this->normalizeText($this->firstValue($room, ['room_type', 'type'])) ?? 'standard',
            currency: $this->normalizeCurrency($this->firstValue($room, ['currency', 'price_currency'])) ?: $fallbackCurrency,
            basePrice: $this->normalizeMoney($this->firstValue($room, ['base_price', 'price', 'room_price'])),
            maxAdults: $maxAdults,
            maxChildren: $maxChildren,
            maxOccupancy: $maxOccupancy,
            sizeSqm: $this->normalizeFloat($this->firstValue($room, ['size_sqm', 'size', 'room_size'])),
            floor: $this->normalizeText($this->firstValue($room, ['floor'])),
            viewType: $this->normalizeText($this->firstValue($room, ['view_type', 'view'])),
            description: $this->normalizeLongText($this->firstValue($room, ['description', 'details'])),
            bedConfiguration: $this->normalizeNameList($this->firstValue($room, ['bed_configuration', 'beds', 'bed_types'])),
            amenityNames: $this->normalizeNameList($this->firstValue($room, ['amenities', 'facilities', 'room_amenities'])),
            quantity: max(1, $this->normalizeInt($this->firstValue($room, ['quantity', 'inventory'])) ?? 1),
            isActive: true,
            sortOrder: $index,
        );
    }

    private function extractRoomsPayload(array $payload): Collection
    {
        foreach (['rooms', 'room_types', 'roomTypes', 'accommodations'] as $key) {
            $value = $payload[$key] ?? null;

            if (is_array($value)) {
                if (array_is_list($value)) {
                    return collect($value)->filter(fn ($room) => is_array($room))->values();
                }

                if (isset($value[0]) && is_array($value[0])) {
                    return collect($value)->filter(fn ($room) => is_array($room))->values();
                }

                return collect($value)
                    ->filter(fn ($room) => is_array($room))
                    ->values();
            }
        }

        return collect();
    }

    private function firstValue(array $payload, array $paths): mixed
    {
        foreach ($paths as $path) {
            $value = data_get($payload, $path);
            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return null;
    }

    private function normalizeText(mixed $value): ?string
    {
        if (is_array($value)) {
            return null;
        }

        $text = trim(strip_tags((string) $value));

        return $text !== '' ? $text : null;
    }

    private function normalizeShortText(mixed $value): ?string
    {
        $text = $this->normalizeText($value);

        return $text ? Str::limit($text, 500, '') : null;
    }

    private function fallbackShortDescription(?string $value): ?string
    {
        return $value ? Str::limit($value, 500, '') : null;
    }

    private function normalizeLongText(mixed $value): ?string
    {
        if (is_array($value)) {
            $value = collect($value)
                ->map(fn ($item) => is_scalar($item) ? trim((string) $item) : null)
                ->filter()
                ->implode(PHP_EOL . PHP_EOL);
        }

        $text = trim(strip_tags((string) $value));

        return $text !== '' ? $text : null;
    }

    private function normalizeEmail(mixed $value): ?string
    {
        $email = $this->normalizeText($value);

        return $email && filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
    }

    private function normalizeUrl(mixed $value): ?string
    {
        $url = $this->normalizeText($value);

        return $url && filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
    }

    private function normalizeUrlList(mixed $value): array
    {
        if (is_string($value)) {
            $value = preg_split('/[\r\n,]+/', $value) ?: [];
        }

        return collect(is_array($value) ? $value : [])
            ->map(function ($item) {
                if (is_array($item)) {
                    return $this->normalizeUrl($this->firstValue($item, ['url', 'src', 'image', 'photo']));
                }

                return $this->normalizeUrl($item);
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function normalizeNameList(mixed $value): array
    {
        if (is_string($value)) {
            $value = preg_split('/[\r\n,]+/', $value) ?: [];
        }

        return collect(is_array($value) ? $value : [])
            ->flatMap(function ($item) {
                if (is_string($item)) {
                    return [$item];
                }

                if (is_array($item)) {
                    if (array_is_list($item)) {
                        return collect($item)
                            ->map(fn ($entry) => $this->normalizeText($entry))
                            ->filter()
                            ->all();
                    }

                    return [$this->normalizeText($this->firstValue($item, ['name', 'title', 'label', 'value', 'text']))];
                }

                return [];
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function normalizePolicies(mixed $value): array
    {
        if (is_string($value)) {
            $value = preg_split('/[\r\n]+/', $value) ?: [];
        }

        return collect(is_array($value) ? $value : [])
            ->map(function ($item, int $index) {
                if (is_string($item)) {
                    $content = $this->normalizeText($item);

                    return $content ? [
                        'title' => 'Policy ' . ($index + 1),
                        'content' => $content,
                    ] : null;
                }

                if (! is_array($item)) {
                    return null;
                }

                $title = $this->normalizeText($this->firstValue($item, ['title', 'name', 'label'])) ?? 'Policy ' . ($index + 1);
                $content = $this->normalizeText($this->firstValue($item, ['content', 'value', 'description', 'text']));

                return $content ? compact('title', 'content') : null;
            })
            ->filter()
            ->values()
            ->all();
    }

    private function normalizeNearbyPlaces(mixed $value): array
    {
        return collect(is_array($value) ? $value : [])
            ->map(function ($item) {
                if (! is_array($item)) {
                    return null;
                }

                $name = $this->normalizeText($this->firstValue($item, ['name', 'title', 'label']));
                $content = $this->normalizeText($this->firstValue($item, ['content', 'description', 'text']));
                $distance = $this->normalizeFloat($this->firstValue($item, ['value', 'distance']));
                $type = $this->normalizeText($this->firstValue($item, ['type', 'unit'])) ?: 'km';

                if (! $name) {
                    return null;
                }

                return [
                    'name' => $name,
                    'content' => $content ?? '',
                    'value' => $distance,
                    'type' => in_array($type, ['m', 'km'], true) ? $type : 'km',
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function normalizeExtraPrices(mixed $value): array
    {
        return collect(is_array($value) ? $value : [])
            ->map(function ($item) {
                if (! is_array($item)) {
                    return null;
                }

                $name = $this->normalizeText($this->firstValue($item, ['name', 'title', 'label']));
                $price = $this->normalizeMoney($this->firstValue($item, ['price', 'amount']));
                $type = $this->normalizeText($this->firstValue($item, ['type', 'price_type'])) ?: 'one_time';

                if (! $name || $price === null) {
                    return null;
                }

                return [
                    'name' => $name,
                    'price' => $price,
                    'type' => in_array($type, ['one_time', 'per_day'], true) ? $type : 'one_time',
                    'per_person' => (bool) ($item['per_person'] ?? false),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function normalizeStarRating(mixed $value): ?int
    {
        $rating = $this->normalizeFloat($value);
        if ($rating === null) {
            return null;
        }

        if ($rating > 5) {
            $rating /= 2;
        }

        return max(1, min(5, (int) round($rating)));
    }

    private function normalizeMoney(mixed $value): ?float
    {
        if (is_array($value)) {
            $value = $this->firstValue($value, ['amount', 'price', 'value']);
        }

        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return round((float) $value, 2);
        }

        $numeric = preg_replace('/[^0-9.\-]+/', '', (string) $value);

        return $numeric !== '' && is_numeric($numeric) ? round((float) $numeric, 2) : null;
    }

    private function normalizeFloat(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (float) $value : null;
    }

    private function normalizeInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        preg_match('/-?\d+/', (string) $value, $matches);

        return isset($matches[0]) ? (int) $matches[0] : null;
    }

    private function normalizeTime(mixed $value): ?string
    {
        $text = $this->normalizeText($value);
        if (! $text) {
            return null;
        }

        if (preg_match('/(\d{1,2}):(\d{2})/', $text, $matches)) {
            return sprintf('%02d:%02d', (int) $matches[1], (int) $matches[2]);
        }

        return null;
    }

    private function normalizeCurrency(mixed $value): string
    {
        $currency = strtoupper((string) $this->normalizeText($value));
        $supported = array_keys(Currency::supported());

        return in_array($currency, $supported, true) ? $currency : Currency::defaultCode();
    }

    private function normalizeCountryCode(?string $value): string
    {
        $value = strtoupper((string) $value);

        if (strlen($value) === 2) {
            return $value;
        }

        $map = [
            'UNITED ARAB EMIRATES' => 'AE',
            'UAE' => 'AE',
            'PAKISTAN' => 'PK',
            'UNITED STATES' => 'US',
            'USA' => 'US',
            'UNITED KINGDOM' => 'GB',
            'UK' => 'GB',
            'SAUDI ARABIA' => 'SA',
            'QATAR' => 'QA',
            'OMAN' => 'OM',
            'BAHRAIN' => 'BH',
            'KUWAIT' => 'KW',
            'TURKEY' => 'TR',
        ];

        return $map[$value] ?? strtoupper((string) config('services.hotel_scraping.fallback_country', 'AE'));
    }

    private function normalizeSlug(mixed $value): ?string
    {
        $text = $this->normalizeText($value);

        return $text ? Str::slug($text) : null;
    }

    private function normalizeSourceUrl(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        $parts = parse_url($value);
        if ($parts === false || empty($parts['host'])) {
            return $value;
        }

        $scheme = strtolower((string) ($parts['scheme'] ?? 'https'));
        $host = strtolower((string) $parts['host']);
        $path = isset($parts['path']) ? rtrim((string) $parts['path'], '/') : '';

        return $scheme . '://' . $host . ($path !== '' ? $path : '');
    }

    private function inferAmenityCategory(string $name): string
    {
        $name = Str::lower($name);

        return match (true) {
            str_contains($name, 'wifi') || str_contains($name, 'internet') => 'connectivity',
            str_contains($name, 'pool') || str_contains($name, 'gym') || str_contains($name, 'fitness') => 'recreation',
            str_contains($name, 'spa') || str_contains($name, 'sauna') => 'wellness',
            str_contains($name, 'parking') || str_contains($name, 'shuttle') => 'transport',
            str_contains($name, 'restaurant') || str_contains($name, 'breakfast') || str_contains($name, 'bar') => 'food_drink',
            str_contains($name, 'safe') || str_contains($name, 'security') => 'safety',
            default => 'general',
        };
    }

    private function inferServiceCategory(string $name): string
    {
        $name = Str::lower($name);

        return match (true) {
            str_contains($name, 'shuttle') || str_contains($name, 'transfer') => 'transport',
            str_contains($name, 'breakfast') || str_contains($name, 'room service') => 'food',
            str_contains($name, 'spa') || str_contains($name, 'massage') => 'wellness',
            str_contains($name, 'laundry') || str_contains($name, 'cleaning') => 'housekeeping',
            str_contains($name, 'business') || str_contains($name, 'meeting') => 'business',
            default => 'general',
        };
    }

    private function extendExecutionTime(int $seconds): void
    {
        if (! function_exists('set_time_limit')) {
            return;
        }

        try {
            @set_time_limit(max(30, $seconds));
        } catch (\Throwable) {
            // Ignore environments where execution time cannot be changed.
        }
    }
}
