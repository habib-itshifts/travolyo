<?php

use Illuminate\Support\Facades\Route;
use Modules\Hotel\Http\Controllers\HotelController;
use Modules\Hotel\Http\Controllers\Api\HotelController as HotelApiController;

Route::get('hotels',                          [HotelController::class, 'index'])->name('hotels.index');
Route::get('hotels/checkout',                 [HotelController::class, 'checkout'])->name('hotels.checkout');
Route::get('hotels/confirmation/{code}',      [HotelController::class, 'confirmation'])->name('hotels.confirmation');

// Web checkout submit — uses web session auth (browser users)
Route::post('hotels/checkout', [HotelApiController::class, 'checkout'])
    ->name('hotels.checkout.submit')
    ->middleware('auth');
