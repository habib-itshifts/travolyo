<?php

use Illuminate\Support\Facades\Route;
use Modules\Flight\Http\Controllers\FlightController;

Route::get('/flights',          [FlightController::class, 'index'])->name('flights.index');
Route::get('/flights/checkout', [FlightController::class, 'checkout'])->name('flights.checkout');