<?php

use Illuminate\Support\Facades\Route;
use Modules\Space\Http\Controllers\Api\SpaceController;

// Public endpoints (no auth required for search/browse)
Route::prefix('spaces')->group(function () {
    Route::post('search', [SpaceController::class, 'search']);
    Route::get('{id}', [SpaceController::class, 'show'])->where('id', '[0-9]+');
    Route::get('{id}/availability', [SpaceController::class, 'availability'])->where('id', '[0-9]+');
    Route::post('book', [SpaceController::class, 'book']);
});

// Authenticated endpoints
Route::middleware(['auth:sanctum'])->prefix('spaces')->group(function () {
    Route::post('checkout', [SpaceController::class, 'checkout']);
    Route::get('order/{orderId}', [SpaceController::class, 'order']);
    Route::post('cancel/{orderId}', [SpaceController::class, 'cancel']);
});
