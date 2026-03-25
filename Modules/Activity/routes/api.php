<?php

use Illuminate\Support\Facades\Route;
use Modules\Activity\Http\Controllers\Api\ActivityController;

Route::prefix('activities')->group(function () {
    Route::post('/search',             [ActivityController::class, 'search'])->name('activities.search');
    Route::get('/details/{offerId}',   [ActivityController::class, 'details'])->name('activities.details');
    Route::post('/prebook',            [ActivityController::class, 'prebook'])->name('activities.prebook');
    Route::post('/checkout',           [ActivityController::class, 'checkout'])->name('activities.checkout')->middleware('auth:sanctum');
    Route::get('/order/{orderId}',     [ActivityController::class, 'order'])->name('activities.order');
    Route::post('/cancel/{orderId}',   [ActivityController::class, 'cancel'])->name('activities.cancel');
});
