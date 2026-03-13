<?php

use Illuminate\Support\Facades\Route;
use Modules\Hotel\Http\Controllers\HotelController;

Route::get('hotels',                          [HotelController::class, 'index'])->name('hotels.index');
Route::get('hotels/checkout',                 [HotelController::class, 'checkout'])->name('hotels.checkout');
Route::get('hotels/confirmation/{code}',      [HotelController::class, 'confirmation'])->name('hotels.confirmation');
