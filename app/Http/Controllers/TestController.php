<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json($this->loadHotelDetails());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function loadHotels()
    {
        $url = 'https://hg-static.hyperguest.com/hotels.json'; // ⚠️ replace with real domain

        $response = Http::withHeaders([
            'Authorization' => 'Bearer 720c616825804c4498f1f21a1d128d4f',
            'Accept-Encoding' => 'gzip, deflate',
            'Accept' => 'application/json',
        ])->get($url);

        $hotelsList = collect($response->json());

        $destination = strtolower('Dubai');

        $destinationHotels = $hotelsList->filter(function ($h) use ($destination) {
                $city = strtolower($h['city'] ?? '');
                $country = strtolower($h['country'] ?? '');

                return $city=== $destination || $country === $destination;
            });

        return response()->json([
            'data' => $destinationHotels

        ]);
    }


    public function loadHotelDetails()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer 720c616825804c4498f1f21a1d128d4f',
            'Accept-Encoding' => 'gzip, deflate',
            'Accept' => 'application/json',
        ])->get("https://search-api.hyperguest.io/2.0/",[
            'checkIn' => '2026-04-01',
            'nights' => 1,
            'guests' => 2,
            'hotelIds' => '19734',
            'customerNationality' => 'US',
        ]);

        

        return $response->json();
    }
}
