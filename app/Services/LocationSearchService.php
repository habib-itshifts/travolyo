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
}
