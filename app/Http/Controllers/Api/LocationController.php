<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LocationSearchService;
use App\Services\LocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function __construct(
        protected LocationService       $locationService,
        protected LocationSearchService $locationSearchService,
    ) {}

    /**
     * GET /api/locations/countries
     */
    public function countries(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $this->locationService->getCountries(),
        ]);
    }

    /**
     * GET /api/locations/cities/{country_code}
     */
    public function cities(string $country_code): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $this->locationService->getCitiesByCountry($country_code),
        ]);
    }

    /**
     * GET /api/locations/airports
     */
    public function airports(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $this->locationService->getAirports(),
        ]);
    }

    /**
     * GET /api/locations/find/{code}
     */
    public function locationByCode(string $code): JsonResponse
    {
        $location = $this->locationSearchService->findByCode($code);

        if (! $location) {
            return response()->json([
                'success' => false,
                'message' => 'Location not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $location,
        ]);
    }

    /**
     * GET /api/locations/airports/nearby
     * Detects user IP server-side, resolves city, returns nearest airport.
     */
    public function nearbyAirport(Request $request): JsonResponse
    {
        $ip = $request->ip();

        // On localhost / private IPs, skip geolocation
        $isLocal = in_array($ip, ['127.0.0.1', '::1'])
            || str_starts_with($ip, '192.168.')
            || str_starts_with($ip, '10.')
            || str_starts_with($ip, '172.');

        if ($isLocal) {
            return response()->json(['success' => false, 'message' => 'Local IP'], 422);
        }

        // Cache per IP for 6 hours to avoid hitting rate limits
        $cacheKey = 'nearby_airport_' . md5($ip);
        $cached = \Illuminate\Support\Facades\Cache::get($cacheKey);
        if ($cached !== null) {
            return response()->json($cached);
        }

        $city = $this->resolveCity($ip);

        if (! $city) {
            $fail = ['success' => false, 'message' => 'City not found'];
            \Illuminate\Support\Facades\Cache::put($cacheKey, $fail, now()->addHour());
            return response()->json($fail, 422);
        }

        $results = $this->locationSearchService->searchAirports($city);

        if (empty($results)) {
            $fail = ['success' => false, 'message' => 'No airport found'];
            \Illuminate\Support\Facades\Cache::put($cacheKey, $fail, now()->addHour());
            return response()->json($fail, 422);
        }

        $result = ['success' => true, 'data' => $results[0]];
        \Illuminate\Support\Facades\Cache::put($cacheKey, $result, now()->addHours(6));

        return response()->json($result);
    }

    /**
     * Try multiple IP geolocation services in order, return city string or null.
     */
    private function resolveCity(string $ip): ?string
    {
        // 1. ip-api.com — 45 req/min free, very reliable
        try {
            $res = \Illuminate\Support\Facades\Http::timeout(5)
                ->get("http://ip-api.com/json/{$ip}?fields=status,city,regionName");

            if ($res->successful() && $res->json('status') === 'success') {
                return $res->json('city') ?? $res->json('regionName') ?? null;
            }
        } catch (\Throwable) {}

        // 2. ipwho.is — free HTTPS fallback
        try {
            $res = \Illuminate\Support\Facades\Http::timeout(5)
                ->get("https://ipwho.is/{$ip}");

            if ($res->successful() && $res->json('success') === true) {
                return $res->json('city') ?? $res->json('region') ?? null;
            }
        } catch (\Throwable) {}

        return null;
    }

    /**
     * GET /api/locations/airports/search?keyword=dub
     */
    public function searchAirports(Request $request): JsonResponse
    {
        $request->validate([
            'keyword' => ['required', 'string', 'min:1', 'max:100'],
        ]);

        return response()->json([
            'success' => true,
            'data'    => $this->locationSearchService->searchAirports($request->keyword),
        ]);
    }

    /**
     * GET /api/locations/hotels/search?keyword=dub
     */
    public function searchHotelDestinations(Request $request): JsonResponse
    {
        $request->validate([
            'keyword' => ['required', 'string', 'min:1', 'max:100'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->locationSearchService->searchHotelDestinations($request->keyword),
        ]);
    }
}
