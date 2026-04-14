<?php

use Illuminate\Support\Facades\Route;
use Modules\Space\Http\Controllers\Api\SpaceController;

// ─── Public ──────────────────────────────────────────────────────────────────
Route::prefix('spaces')->group(function () {
    // 1. Search — returns paginated list of available spaces
    Route::post('search', [SpaceController::class, 'search'])->name('spaces.search');

    // 2. Show — full detail for a single space (detail page)
    Route::get('{id}', [SpaceController::class, 'show'])
        ->where('id', '[0-9]+')
        ->name('spaces.show');

    // 3. Availability — calendar/blocked dates for a space (detail page calendar)
    Route::get('{id}/availability', [SpaceController::class, 'availability'])
        ->where('id', '[0-9]+')
        ->name('spaces.availability');

    // 4. Prebook — validates availability & constraints, calculates pricing,
    //              stores checkout cache, returns checkout_token + converted price
    Route::post('prebook', [SpaceController::class, 'prebook'])->name('spaces.prebook');
});

// ─── Authenticated ────────────────────────────────────────────────────────────
Route::middleware(['auth:sanctum'])->prefix('spaces')->group(function () {
    // 5. Checkout — collects guest details, creates draft booking, initiates payment
    Route::post('checkout', [SpaceController::class, 'checkout'])->name('spaces.checkout');

    // 6. Order — retrieve booking details by order code
    Route::get('order/{orderId}', [SpaceController::class, 'order'])->name('spaces.order');

    // 7. Cancel — cancel a booking by order code
    Route::post('cancel/{orderId}', [SpaceController::class, 'cancel'])->name('spaces.cancel');
});
