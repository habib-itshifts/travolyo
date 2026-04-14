<?php

use Illuminate\Support\Facades\Route;
use Modules\Space\Http\Controllers\SpaceController;
use Modules\Space\Http\Controllers\Api\SpaceController as SpaceApiController;

// Public-facing homes & apartments pages
Route::get('homes',                       [SpaceController::class, 'index'])->name('homes.index');
Route::get('homes/detail',                [SpaceController::class, 'detail'])->name('homes.detail');
Route::get('homes/checkout',              [SpaceController::class, 'checkout'])->name('homes.checkout');
Route::get('homes/confirmation/{code}',   [SpaceController::class, 'confirmation'])->name('homes.confirmation');

// Web checkout submit — uses web session auth (browser users)
Route::post('homes/checkout', [SpaceApiController::class, 'checkout'])
    ->name('homes.checkout.submit')
    ->middleware('auth');

// Admin CRUD: Modules/Admin/routes/web.php (admin.spaces.*)
// Vendor CRUD: Modules/Vendor/routes/web.php (vendor.spaces.*)
// API: Modules/Space/routes/api.php (spaces/*)
