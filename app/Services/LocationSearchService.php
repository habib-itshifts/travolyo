<?php

namespace App\Services;

class LocationSearchService
{
    public function __construct(protected LocationService $locationService) {}

    /**
     * Search airports by keyword (IATA code or city name).
     * Returns max 10 results for AJAX autocomplete.
     */
    public function searchAirports(string $keyword): array
    {
        $keyword = mb_strtolower(trim($keyword));

        if ($keyword === '') {
            return [];
        }

        return collect($this->locationService->getAirports())
            ->filter(
                fn($a) => str_contains(mb_strtolower((string) ($a['IATA_CODE'] ?? '')), $keyword)
                       || str_contains(mb_strtolower((string) ($a['CITY'] ?? '')), $keyword)
            )
            ->take(10)
            ->map(fn($a) => [
                'code'    => (string) ($a['IATA_CODE'] ?? ''),
                'name'    => (string) ($a['AIRPORT'] ?? ''),
                'city'    => (string) ($a['CITY'] ?? ''),
                'country' => (string) ($a['COUNTRY'] ?? ''),
                'label'   => trim(($a['IATA_CODE'] ?? '') . ' - ' . ($a['CITY'] ?? '') . ', ' . ($a['COUNTRY'] ?? '')),
            ])
            ->values()
            ->all();
    }

    /**
     * Find a location by IATA code or city name (exact, case-insensitive).
     * e.g. "DXB" or "Dubai" both return the matching airport(s).
     */
    public function findByCode(string $code): ?array
    {
        $code = mb_strtolower(trim($code));

        if ($code === '') {
            return null;
        }

        $airport = collect($this->locationService->getAirports())
            ->first(fn($a) =>
                mb_strtolower((string) ($a['IATA_CODE'] ?? '')) === $code
                || mb_strtolower((string) ($a['CITY'] ?? '')) === $code
            );

        if (! $airport) {
            return null;
        }

        return [
            'code'    => (string) ($airport['IATA_CODE'] ?? ''),
            'name'    => (string) ($airport['AIRPORT'] ?? ''),
            'city'    => (string) ($airport['CITY'] ?? ''),
            'country' => (string) ($airport['COUNTRY'] ?? ''),
            'label'   => trim(($airport['IATA_CODE'] ?? '') . ' - ' . ($airport['CITY'] ?? '') . ', ' . ($airport['COUNTRY'] ?? '')),
        ];
    }

    /**
     * Search hotel destinations from the local JSON source.
     */
    public function searchHotelDestinations(string $keyword): array
    {
        $keyword = mb_strtolower(trim($keyword));

        if ($keyword === '') {
            return [];
        }

        return collect($this->locationService->getHotelDestinations())
            ->map(function (array $item) use ($keyword) {
                $score = $this->scoreHotelDestination($item, $keyword);

                if ($score === null) {
                    return null;
                }

                return [
                    'score' => $score,
                    'item' => $item,
                ];
            })
            ->filter()
            ->sortBy(fn (array $row) => sprintf(
                '%04d_%s',
                $row['score'],
                mb_strtolower((string) ($row['item']['display_name'] ?? $row['item']['destination'] ?? ''))
            ))
            ->take(12)
            ->map(fn (array $row) => $row['item'])
            ->values()
            ->all();
    }

    private function scoreHotelDestination(array $item, string $keyword): ?int
    {
        $type = (string) ($item['type'] ?? 'city');
        $destination = mb_strtolower(trim((string) ($item['destination'] ?? '')));
        $city = mb_strtolower(trim((string) ($item['city'] ?? '')));
        $country = mb_strtolower(trim((string) ($item['country'] ?? '')));
        $region = mb_strtolower(trim((string) ($item['region'] ?? '')));
        $countryCode = mb_strtolower(trim((string) ($item['country_code'] ?? '')));
        $location = mb_strtolower(trim((string) ($item['location'] ?? '')));
        $displayName = mb_strtolower(trim((string) ($item['display_name'] ?? '')));

        if ($type === 'city' && ($city === $keyword || $destination === $keyword)) {
            return 0;
        }

        if ($type === 'country' && ($country === $keyword || $destination === $keyword)) {
            return 5;
        }

        if ($location !== '' && $location === $keyword) {
            return 8;
        }

        if ($type === 'city' && $city !== '' && str_starts_with($city, $keyword)) {
            return 10;
        }

        if ($type === 'country' && $country !== '' && str_starts_with($country, $keyword)) {
            return 15;
        }

        if ($destination !== '' && str_starts_with($destination, $keyword)) {
            return 20;
        }

        if ($country !== '' && str_starts_with($country, $keyword)) {
            return 25;
        }

        if ($displayName !== '' && str_contains($displayName, $keyword)) {
            return 30;
        }

        if ($city !== '' && str_contains($city, $keyword)) {
            return 35;
        }

        if ($country !== '' && str_contains($country, $keyword)) {
            return 40;
        }

        if ($region !== '' && str_contains($region, $keyword)) {
            return 50;
        }

        if ($countryCode !== '' && str_contains($countryCode, $keyword)) {
            return 55;
        }

        if ($location !== '' && str_contains($location, $keyword)) {
            return 60;
        }

        return null;
    }
}
