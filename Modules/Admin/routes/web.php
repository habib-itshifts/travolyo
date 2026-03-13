<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\DashboardController;
use Modules\Admin\Http\Controllers\HotelController;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'admin'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Hotels
        Route::resource('hotels', HotelController::class);
        Route::post('hotels/{id}/restore', [HotelController::class, 'restore'])->name('hotels.restore');
    });
