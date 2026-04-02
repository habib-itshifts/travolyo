<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class LocationService
{
    private const CACHE_TTL = 24 * 60 * 60;
    private const HOTEL_DESTINATIONS_FILE = 'data/world country and city locations.json';

    /**
     * Get unique countries list.
     */
    public function getCountries(): array
    {
        return Cache::remember('location.countries', self::CACHE_TTL, function () {
            $response = Http::timeout(120)
                ->withHeaders([
                    'auth-api-key' => config('travolyo_b2b.auth_api_key'),
                    'Content-Type' => 'application/json',
                ])
                ->get(rtrim(config('travolyo_b2b.base_url'), '/') . '/api/v1/hotels/destinations');

            if (! $response->successful()) {
                return [];
            }

            $destinations = $response->json('destinations') ?? $response->json('data.destinations') ?? [];

            return collect($destinations)
                ->map(fn($item) => [
                    'country_code' => $item['country_code'] ?? '',
                    'country_name' => trim($item['country'] ?? ''),
                ])
                ->filter(fn($item) => $item['country_code'] && $item['country_name'])
                ->unique('country_code')
                ->values()
                ->all();
        });
    }

    /**
     * Get cities filtered by a single country code.
     */
    public function getCitiesByCountry(string $countryCode): array
    {
        $upper = strtoupper($countryCode);

        return collect($this->getCitiesByCountries())
            ->filter(fn($item) => strtoupper($item['country_code']) === $upper)
            ->values()
            ->all();
    }

    /**
     * Fetch all destinations from the API and cache the full JSON response.
     * Returns every city across all countries mapped to a standard shape.
     */
    public function getCitiesByCountries(): array
    {
        return Cache::remember('location.cities.all', self::CACHE_TTL, function () {
            $response = Http::timeout(120)
                ->withHeaders([
                    'auth-api-key' => config('travolyo_b2b.auth_api_key'),
                    'Content-Type' => 'application/json',
                ])
                ->get(rtrim(config('travolyo_b2b.base_url'), '/') . '/api/v1/hotels/destinations');

            if (! $response->successful()) {
                return [];
            }

            $destinations = $response->json('destinations') ?? $response->json('data.destinations') ?? [];

            return collect($destinations)
                ->map(fn($item) => [
                    'city_code'    => $item['city_code'] ?? '',
                    'city_name'    => trim($item['city'] ?? ''),
                    'country_code' => trim($item['country_code'] ?? ''),
                    'country_name' => trim($item['country'] ?? ''),
                ])
                ->filter(fn($item) => $item['city_code'] && $item['city_name'])
                ->unique('city_code')
                ->values()
                ->all();
        });
    }

    /**
     * Get all airports from public/data/airports.json.
     */
    public function getAirports(): array
    {
        $file = public_path('data/worldwide-airports.json');

        if (! file_exists($file)) {
            return [];
        }

        return json_decode(file_get_contents($file), true) ?? [];
    }

    /**
     * Get hotel destinations from the local JSON file.
     *
     * Each item includes:
     * - type: city|country
     * - destination
     * - city
     * - country
     * - country_code
     * - location
     * - region
     * - display_name
     */
    public function getHotelDestinations(): array
    {
        $file = public_path(self::HOTEL_DESTINATIONS_FILE);

        if (! is_file($file)) {
            return [];
        }

        $raw = (string) file_get_contents($file);
        $raw = preg_replace('/^\xEF\xBB\xBF/', '', $raw) ?? $raw;
        $payload = json_decode($raw, true);
        $destinations = data_get($payload, 'destinations', []);

        if (! is_array($destinations)) {
            return [];
        }

        return collect($destinations)
            ->map(function (array $item) {
                $type = ($item['type'] ?? 'city') === 'country' ? 'country' : 'city';
                $city = trim((string) ($item['city'] ?? ''));
                $country = trim((string) ($item['country'] ?? ''));
                $destination = trim((string) ($item['destination'] ?? ($type === 'country' ? $country : $city)));
                $countryCode = strtoupper(trim((string) ($item['country_code'] ?? '')));
                $location = strtoupper(trim((string) ($item['location'] ?? '')));
                $region = trim((string) ($item['region'] ?? ''));

                $displayName = $type === 'country'
                    ? $destination
                    : collect([$city ?: $destination, $country])->filter()->implode(', ');

                return [
                    'type' => $type,
                    'destination' => $destination,
                    'city' => $city,
                    'country' => $country,
                    'country_code' => $countryCode,
                    'location' => $location,
                    'region' => $region,
                    'display_name' => $displayName,
                ];
            })
            ->filter(fn (array $item) => $item['destination'] !== '' && $item['country'] !== '')
            ->unique(fn (array $item) => implode('|', [
                $item['type'],
                mb_strtolower($item['destination']),
                mb_strtolower($item['country']),
            ]))
            ->values()
            ->all();
    }
}
