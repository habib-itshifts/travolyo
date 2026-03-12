<?php

use Illuminate\Support\Facades\Route;
use Modules\Flight\Http\Controllers\FlightController;

Route::get('/flights', [FlightController::class, 'index'])->name('flights.index');