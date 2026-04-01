<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\LocationController;
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
    Route::get('/countries',        [LocationController::class, 'countries'])->name('api.locations.countries');
    Route::get('/cities/{country_code}', [LocationController::class, 'cities'])->name('api.locations.cities');
    Route::get('/airports',         [LocationController::class, 'airports'])->name('api.locations.airports');
    Route::get('/airports/search',  [LocationController::class, 'searchAirports'])->name('api.locations.airports.search');
    Route::get('/hotels/search',    [LocationController::class, 'searchHotelDestinations'])->name('api.locations.hotels.search');
    Route::get('/find/{code}',      [LocationController::class, 'locationByCode'])->name('api.locations.find');
});
