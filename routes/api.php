<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\LocationController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| All routes here are prefixed with /api automatically.
| Flight routes live in Modules/Flight/routes/api.php
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Auth (Sanctum token-based — for mobile app)
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->name('api.auth.')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login',    [AuthController::class, 'login'])->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/me',      [AuthController::class, 'me'])->name('me');
    });
});

Route::get('/bookings/{code}', [BookingController::class, 'show'])->name('api.bookings.show');

Route::prefix('locations')->group(function () {
    Route::get('/search',           [LocationController::class, 'searchHotelDestinations'])->name('api.locations.search');
    Route::get('/countries',        [LocationController::class, 'countries'])->name('api.locations.countries');
    Route::get('/cities/{country_code}', [LocationController::class, 'cities'])->name('api.locations.cities');
    Route::get('/airports',         [LocationController::class, 'airports'])->name('api.locations.airports');
    Route::get('/airports/nearby',  [LocationController::class, 'nearbyAirport'])->name('api.locations.airports.nearby');
    Route::get('/airports/search',  [LocationController::class, 'searchAirports'])->name('api.locations.airports.search');
    Route::get('/find/{code}',      [LocationController::class, 'locationByCode'])->name('api.locations.find');
});

Route::post('/hyperguest-test-booking', function () {
    $payload = [
        'dates' => [
            'from' => now()->addDays(30)->format('Y-m-d'),
            'to'   => now()->addDays(31)->format('Y-m-d'),
        ],
        'propertyId' => 19912,
        'leadGuest' => [
            'birthDate' => '1991-01-01',
            'contact' => [
                'address' => '308 Negra Arroyo Lane',
                'city'    => 'Albuquerque',
                'country' => 'US',
                'email'   => 'test@example.test',
                'phone'   => '555-6162',
                'state'   => 'New Mexico',
                'zip'     => '87111',
            ],
            'name' => [
                'first' => 'Test',
                'last'  => 'Example',
            ],
            'title' => 'MR',
        ],
        'reference' => [
            'agency' => 'travolyo-test-' . uniqid(),
        ],
        'paymentDetails' => [
            'type' => 'credit_card',
            'details' => [
                'number' => '4111111111111111',
                'cvv'    => '123',
                'expiry' => [
                    'month' => '1',
                    'year'  => '2028',
                ],
                'name' => [
                    'first' => 'Test',
                    'last'  => 'Example',
                ],
                'charge' => false,
            ],
        ],
        'rooms' => [[
            'roomCode' => 'SGL',
            'rateCode' => 'BAR',
            'expectedPrice' => [
                'amount'   => 1000,
                'currency' => 'EUR',
            ],
            'guests' => [
                [
                    'birthDate' => '1978-01-01',
                    'contact' => [
                        'address' => '308 Negra Arroyo Lane',
                        'city'    => 'Albuquerque',
                        'country' => 'US',
                        'email'   => 'test@example.test',
                        'phone'   => '555-6162',
                        'state'   => 'New Mexico',
                        'zip'     => '87111',
                    ],
                    'name' => [
                        'first' => 'test',
                        'last'  => 'test',
                    ],
                    'title' => 'MR',
                ],
                [
                    'birthDate' => '1978-01-01',
                    'name' => [
                        'first' => 'test',
                        'last'  => 'test',
                    ],
                    'title' => 'MR',
                ],
            ],
            'specialRequests' => [
                'Non-smoking room preferred',
                'Twin bed please',
            ],
        ]],
        'meta' => [
            ['key' => 'Source', 'value' => 'Travolyo Test'],
        ],
        'isTest'       => true,
        'groupBooking' => false,
    ];

    $response = Http::withHeaders([
        'Accept-Encoding' => 'gzip, deflate',
        'Accept'          => 'application/json',
        'Authorization'   => 'Bearer 720c616825804c4498f1f21a1d128d4f',
    ])->timeout(30)
      ->post('https://book-api.hyperguest.com/2.0/booking/create', $payload);

    return response()->json([
        'status'   => $response->status(),
        'payload'  => $payload,
        'response' => $response->json(),
    ]);
});
