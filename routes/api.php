<?php

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

Route::get('/bookings/{code}', [BookingController::class, 'show'])->name('api.bookings.show');

Route::prefix('locations')->group(function () {
    Route::get('/countries',        [LocationController::class, 'countries'])->name('api.locations.countries');
    Route::get('/cities/{country_code}', [LocationController::class, 'cities'])->name('api.locations.cities');
    Route::get('/airports',         [LocationController::class, 'airports'])->name('api.locations.airports');
    Route::get('/airports/search',  [LocationController::class, 'searchAirports'])->name('api.locations.airports.search');
});