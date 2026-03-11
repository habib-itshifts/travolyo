<?php

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
Route::prefix('locations')->group(function () {
    Route::get('/countries', [LocationController::class, 'countries'])->name('api.locations.countries');
    Route::get('/cities/{country_code}', [LocationController::class, 'cities'])->name('api.locations.cities');
    Route::get('/airports',        [LocationController::class, 'airports'])->name('api.locations.airports');
    Route::get('/airports/search', [LocationController::class, 'searchAirports'])->name('api.locations.airports.search');
});
