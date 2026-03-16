<?php

use Illuminate\Support\Facades\Route;
use Modules\Vendor\Http\Controllers\DashboardController;
use Modules\Vendor\Http\Controllers\ProfileController;

Route::prefix('vendor')
    ->name('vendor.')
    ->middleware(['auth', 'verified', 'vendor'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Profile
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    });
