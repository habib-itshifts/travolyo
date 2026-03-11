<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class LocationService
{
    private const CACHE_TTL = 24 * 60 * 60;

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
}
