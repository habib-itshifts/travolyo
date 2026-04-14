<?php

use Illuminate\Support\Facades\Route;
use Modules\Space\Http\Controllers\Api\SpaceController;

// Public endpoints (no auth required for search/browse)
Route::prefix('spaces')->group(function () {
    Route::post('search', [SpaceController::class, 'search'])->name('spaces.search');
    Route::get('{id}', [SpaceController::class, 'show'])->where('id', '[0-9]+')->name('spaces.show');
    Route::get('{id}/availability', [SpaceController::class, 'availability'])->where('id', '[0-9]+')->name('spaces.availability');
    Route::post('book', [SpaceController::class, 'book'])->name('spaces.book');
});

// Authenticated endpoints
Route::middleware(['auth:sanctum'])->prefix('spaces')->group(function () {
    Route::post('checkout', [SpaceController::class, 'checkout'])->name('spaces.checkout');
    Route::get('order/{orderId}', [SpaceController::class, 'order'])->name('spaces.order');
    Route::post('cancel/{orderId}', [SpaceController::class, 'cancel'])->name('spaces.cancel');
});
