<?php

use Illuminate\Support\Facades\Route;
use Modules\Vendor\Http\Controllers\DashboardController;

Route::prefix('vendor')
    ->name('vendor.')
    ->middleware(['auth', 'verified', 'vendor'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });
