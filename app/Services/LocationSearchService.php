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
            ->map(function (array $airport) use ($keyword) {
                $score = $this->scoreAirport($airport, $keyword);

                if ($score === null) {
                    return null;
                }

                return [
                    'score' => $score,
                    'item' => [
                        'code'    => (string) ($airport['IATA_CODE'] ?? ''),
                        'name'    => (string) ($airport['AIRPORT'] ?? ''),
                        'city'    => (string) ($airport['CITY'] ?? ''),
                        'country' => (string) ($airport['COUNTRY'] ?? ''),
                        'label'   => trim(($airport['IATA_CODE'] ?? '') . ' - ' . ($airport['CITY'] ?? '') . ', ' . ($airport['COUNTRY'] ?? '')),
                    ],
                ];
            })
            ->filter()
            ->sortBy(fn (array $row) => sprintf('%04d_%s', $row['score'], mb_strtolower($row['item']['label'] ?? '')))
            ->take(10)
            ->map(fn (array $row) => $row['item'])
            ->values()
            ->all();
    }

    private function scoreAirport(array $airport, string $keyword): ?int
    {
        $code = mb_strtolower(trim((string) ($airport['IATA_CODE'] ?? '')));
        $city = mb_strtolower(trim((string) ($airport['CITY'] ?? '')));
        $name = mb_strtolower(trim((string) ($airport['AIRPORT'] ?? '')));
        $country = mb_strtolower(trim((string) ($airport['COUNTRY'] ?? '')));

        if ($code === '' && $city === '' && $name === '') {
            return null;
        }

        if ($code === $keyword || $city === $keyword) {
            return 0;
        }

        if ($code !== '' && str_starts_with($code, $keyword)) {
            return 5;
        }

        if ($city !== '' && str_starts_with($city, $keyword)) {
            return 8;
        }

        if ($name !== '' && str_contains($name, $keyword)) {
            return 10;
        }

        if ($city !== '' && str_contains($city, $keyword)) {
            return 12;
        }

        if ($country !== '' && str_contains($country, $keyword)) {
            return 20;
        }

        $compactKeyword = preg_replace('/[^a-z0-9]/', '', $keyword);
        if ($compactKeyword === '') {
            return null;
        }

        foreach (array_filter([$code, $city, $name, $country]) as $candidate) {
            foreach (preg_split('/[\s,\-\/]+/', $candidate) ?: [] as $part) {
                $partCompact = preg_replace('/[^a-z0-9]/', '', $part);
                if ($partCompact === '') {
                    continue;
                }

                if (str_starts_with($partCompact, $compactKeyword)) {
                    return 25;
                }

                if (mb_strlen($compactKeyword) <= 3 && $this->shortAirportMatch($partCompact, $compactKeyword)) {
                    return 30;
                }

                $distance = levenshtein($compactKeyword, mb_substr($partCompact, 0, max(mb_strlen($compactKeyword), 4)));
                if ($distance <= 2 && abs(mb_strlen($partCompact) - mb_strlen($compactKeyword)) <= 4) {
                    return 35 + $distance;
                }
            }
        }

        return null;
    }

    private function shortAirportMatch(string $haystack, string $keyword): bool
    {
        if ($haystack === '' || $keyword === '') {
            return false;
        }

        $keywordPrefix = mb_substr($keyword, 0, 2);
        $haystackPrefix = mb_substr($haystack, 0, 2);

        return $keywordPrefix !== '' && $keywordPrefix === $haystackPrefix;
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

        $searchValues = array_filter([
            $city,
            $destination,
            $displayName,
            $country,
            $region,
        ]);

        $fuzzyScore = $this->matchesSearchKeyword($searchValues, $keyword);
        if ($fuzzyScore !== null) {
            return $fuzzyScore;
        }

        return null;
    }

    /**
     * Match partial words in city/country names without pulling random results.
     */
    private function matchesSearchKeyword(array $haystacks, string $keyword): ?int
    {
        $keyword = trim($keyword);

        if ($keyword === '') {
            return null;
        }

        $normalizedKeyword = mb_strtolower(preg_replace('/\s+/', ' ', $keyword));
        $keywordCompact = preg_replace('/[^a-z0-9]/', '', $normalizedKeyword);
        $bestScore = null;
        $shortKeyword = mb_strlen($keywordCompact) <= 3;

        foreach ($haystacks as $haystack) {
            $haystack = mb_strtolower(trim((string) $haystack));
            if ($haystack === '') {
                continue;
            }

            $haystackCompact = preg_replace('/[^a-z0-9]/', '', $haystack);

            if ($haystackCompact !== '' && str_contains($haystackCompact, $keywordCompact)) {
                return 20;
            }

            foreach (preg_split('/[\s,\-\/]+/', $haystack) ?: [] as $part) {
                $part = trim($part);
                if ($part === '') {
                    continue;
                }

                $partCompact = preg_replace('/[^a-z0-9]/', '', $part);
                $score = $this->scoreCandidate($partCompact, $keywordCompact);
                if ($score === null) {
                    continue;
                }

                if ($shortKeyword && $this->matchesShortKeyword($partCompact, $keywordCompact)) {
                    return 0;
                }

                $bestScore = $bestScore === null ? $score : min($bestScore, $score);
                if ($bestScore === 0) {
                    return 0;
                }
            }
        }

        return $bestScore;
    }

    private function scoreCandidate(string $haystack, string $keyword): ?int
    {
        if ($haystack === '' || $keyword === '') {
            return null;
        }

        if (str_starts_with($haystack, $keyword)) {
            return 0;
        }

        $shortHaystack = mb_substr($haystack, 0, max(mb_strlen($keyword), 4));
        $distance = levenshtein($keyword, $shortHaystack);

        if (mb_strlen($keyword) >= 3 && soundex($haystack) === soundex($keyword)) {
            $distance = min($distance, 2);
        }

        if (abs(mb_strlen($haystack) - mb_strlen($keyword)) > 5) {
            return null;
        }

        return ($distance * 10) + abs(mb_strlen($haystack) - mb_strlen($keyword));
    }

    private function matchesShortKeyword(string $haystack, string $keyword): bool
    {
        if ($haystack === '' || $keyword === '') {
            return false;
        }

        if (str_starts_with($haystack, $keyword)) {
            return true;
        }

        $keywordPrefix = mb_substr($keyword, 0, 2);
        $haystackPrefix = mb_substr($haystack, 0, 2);

        if ($keywordPrefix !== '' && $keywordPrefix === $haystackPrefix) {
            return true;
        }

        $normalizedHaystack = preg_replace('/[aeiouy]/', '', $haystack);
        $normalizedKeyword = preg_replace('/[aeiouy]/', '', $keyword);

        return $normalizedHaystack !== '' && $normalizedHaystack === $normalizedKeyword;
    }

}
