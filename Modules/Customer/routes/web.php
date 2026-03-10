<?php

use Illuminate\Support\Facades\Route;
use Modules\Customer\Http\Controllers\DashboardController;

Route::prefix('customer')
    ->name('customer.')
    ->middleware(['auth', 'verified', 'customer'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });
