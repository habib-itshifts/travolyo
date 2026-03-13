<?php

use Illuminate\Support\Facades\Route;
use Modules\Hotel\Http\Controllers\Api\HotelController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::post('hotels/search',   [HotelController::class, 'search'])->name('hotels.search');
    Route::post('hotels/prebook',  [HotelController::class, 'prebook'])->name('hotels.prebook');
    Route::post('hotels/checkout', [HotelController::class, 'checkout'])->name('hotels.checkout');
    Route::get('hotels/order/{orderId}',  [HotelController::class, 'order'])->name('hotels.order');
    Route::post('hotels/cancel/{orderId}', [HotelController::class, 'cancel'])->name('hotels.cancel');
});
