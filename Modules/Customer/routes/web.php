<?php

use Illuminate\Support\Facades\Route;
use Modules\Customer\Http\Controllers\BookingController;
use Modules\Customer\Http\Controllers\CustomerController;
use Modules\Customer\Http\Controllers\DashboardController;
use Modules\Customer\Http\Controllers\ProfileController;

Route::prefix('customer')
    ->name('customer.')
    ->middleware(['auth', 'verified', 'customer'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Bookings
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');

        // Vendor request
        Route::post('/request-vendor', [CustomerController::class, 'requestVendor'])->name('request-vendor');

        // Profile
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    });
