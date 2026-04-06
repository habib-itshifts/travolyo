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

        try {
            $geo = \Illuminate\Support\Facades\Http::timeout(4)
                ->withHeaders(['Accept' => 'application/json'])
                ->get("https://ipapi.co/{$ip}/json/");

            if (! $geo->successful()) {
                return response()->json(['success' => false, 'message' => 'Geo lookup failed'], 422);
            }

            $city = $geo->json('city') ?? $geo->json('region') ?? null;
        } catch (\Throwable) {
            return response()->json(['success' => false, 'message' => 'Geo error'], 422);
        }

        if (! $city) {
            return response()->json(['success' => false, 'message' => 'City not found'], 422);
        }

        $results = $this->locationSearchService->searchAirports($city);

        if (empty($results)) {
            return response()->json(['success' => false, 'message' => 'No airport found'], 422);
        }

        return response()->json([
            'success' => true,
            'data'    => $results[0],
        ]);
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
