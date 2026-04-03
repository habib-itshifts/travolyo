<?php

use Illuminate\Support\Facades\Route;
use Modules\Flight\Http\Controllers\Api\FlightController;

Route::prefix('flights')->group(function () {
    Route::post('/search',             [FlightController::class, 'search'])->name('flights.search');
    Route::post('/prebook',            [FlightController::class, 'prebook'])->name('flights.prebook');
    Route::post('/checkout',           [FlightController::class, 'checkout'])->name('flights.checkout')->middleware('auth:sanctum');
    Route::post('/pay',                [FlightController::class, 'pay'])->name('flights.pay');
    Route::get('/orders/{orderId}',    [FlightController::class, 'order'])->name('flights.order');
    Route::delete('/orders/{orderId}', [FlightController::class, 'cancel'])->name('flights.cancel');
});