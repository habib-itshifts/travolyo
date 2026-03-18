<?php

use Illuminate\Support\Facades\Route;
use Modules\Hotel\Http\Controllers\Api\HotelController;

Route::prefix('hotels')->group(function () {
    Route::post('/search',            [HotelController::class, 'search'])->name('hotels.search');
    Route::post('/rooms',             [HotelController::class, 'rooms'])->name('hotels.rooms');
    Route::post('/prebook',           [HotelController::class, 'prebook'])->name('hotels.prebook');
    Route::post('/checkout',          [HotelController::class, 'checkout'])->name('hotels.checkout');
    Route::get('/order/{orderId}',    [HotelController::class, 'order'])->name('hotels.order');
    Route::post('/cancel/{orderId}',  [HotelController::class, 'cancel'])->name('hotels.cancel');
});