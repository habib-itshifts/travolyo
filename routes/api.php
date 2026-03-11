<?php

use App\Http\Controllers\Api\FlightController;
use App\Http\Controllers\Api\LocationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| All routes here are prefixed with /api automatically.
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Location Routes (public — no auth required)
| Used by the search widget via AJAX
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| Flight Routes (public — no auth required for search)
|--------------------------------------------------------------------------
*/
Route::prefix('flights')->group(function () {
    Route::post('/search',          [FlightController::class, 'search'])->name('api.flights.search');
    Route::post('/prebook',         [FlightController::class, 'prebook'])->name('api.flights.prebook');
    Route::post('/checkout',        [FlightController::class, 'checkout'])->name('api.flights.checkout');
    Route::post('/pay',             [FlightController::class, 'pay'])->name('api.flights.pay');
    Route::get('/orders/{orderId}', [FlightController::class, 'order'])->name('api.flights.order');
    Route::delete('/orders/{orderId}', [FlightController::class, 'cancel'])->name('api.flights.cancel');
});

Route::prefix('locations')->group(function () {
    Route::get('/countries', [LocationController::class, 'countries'])->name('api.locations.countries');
    Route::get('/cities/{country_code}', [LocationController::class, 'cities'])->name('api.locations.cities');
    Route::get('/airports',        [LocationController::class, 'airports'])->name('api.locations.airports');
    Route::get('/airports/search', [LocationController::class, 'searchAirports'])->name('api.locations.airports.search');
});
