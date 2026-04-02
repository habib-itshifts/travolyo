<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
   

        $path = public_path('data/hyperguest/hotels.json');

        // true → Associative Array
        $jsonString = json_decode(\File::get($path),true);
        // return response()->json($jsonString['results'][0]['propertyInfo']['name']);

        $rooms = $jsonString['results'][0]['rooms'];
        // return response()->json($rooms[0]);
        // return response()->json($rooms[0]['ratePlans'][4]['payment']);

        return response()->json([
            'rooms' => [
                [
                    'ID' => $rooms[0]['roomId'],
                    'Name' => $rooms[0]['roomName'],

                    'types' => [
                        [
                            'Name' => $rooms[0]['ratePlans'][0]['board'],
                            'Price' => currency($rooms[0]['ratePlans'][0]['prices']['sell']['price'], 'AED','AED', false),
                        ],
                        [
                            'Name' => $rooms[0]['ratePlans'][1]['board'],
                            'Price' => $rooms[0]['ratePlans'][1]['prices']['sell']['price'],
                        ],
                        [
                            'Name' => $rooms[0]['ratePlans'][2]['board'],
                            'Price' => $rooms[0]['ratePlans'][2]['prices']['sell']['price'],
                        ],
                        [
                            'Name' => $rooms[0]['ratePlans'][3]['board'],
                            'Price' => $rooms[0]['ratePlans'][3]['prices']['sell']['price'],
                        ],
                    ]
                ]
            ]
            



            // 'ID' => $rooms[0]['roomId'],
            //   'Room Name' => $rooms[0]['roomName'], 
            //   'Room' => 
            //   [
            //     'Type' => $rooms[0]['settings']['beddingConfigurations'][0]['type'],
            //     'Type' => $rooms[0]['ratePlans'][0]['board'],
            //   ], 
              
        ]);






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
            'checkIn' => '2026-04-02',
            'nights' => 1,
            'guests' => 2,
            'hotelIds' => '59363',
            'customerNationality' => 'US',
        ]);

        

        return $response->json();
    }
}
