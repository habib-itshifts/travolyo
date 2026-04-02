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
