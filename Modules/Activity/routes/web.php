<?php

use Illuminate\Support\Facades\Route;
use Modules\Activity\Http\Controllers\ActivityController;

Route::prefix('activities')->name('activities.')->group(function () {
    Route::get('/', [ActivityController::class, 'index'])->name('index');
    Route::get('/{activity}/checkout', [ActivityController::class, 'checkout'])->name('checkout');
    Route::post('/{activity}/checkout/pay', [ActivityController::class, 'pay'])->name('pay');
    Route::get('/booking/{code}', [ActivityController::class, 'bookingDetail'])->name('booking.detail');
    Route::get('/{activity:slug}', [ActivityController::class, 'show'])->name('show');
});
