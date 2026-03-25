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
}
