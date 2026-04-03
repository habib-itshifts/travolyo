<?php

use Illuminate\Support\Facades\Route;
use Modules\Flight\Http\Controllers\FlightController;
use Modules\Flight\Http\Controllers\Api\FlightController as FlightApiController;

Route::get('/flights',          [FlightController::class, 'index'])->name('flights.index');
Route::get('/flights/checkout', [FlightController::class, 'checkout'])->name('flights.checkout');

// Web checkout submit — uses web session auth (browser users)
Route::post('/flights/checkout', [FlightApiController::class, 'checkout'])
    ->name('flights.checkout.submit')
    ->middleware('auth');