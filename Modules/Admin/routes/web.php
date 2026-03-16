<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AmenityController;
use Modules\Admin\Http\Controllers\CurrencyController;
use Modules\Admin\Http\Controllers\DashboardController;
use Modules\Admin\Http\Controllers\HotelController;
use Modules\Admin\Http\Controllers\HotelRoomController;
use Modules\Admin\Http\Controllers\MediaController;
use Modules\Admin\Http\Controllers\ServiceController;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'admin'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Media
        Route::get('/media', [MediaController::class, 'index'])->name('media.index');
        Route::get('/media/browser', [MediaController::class, 'browser'])->name('media.browser');
        Route::post('/media/upload', [MediaController::class, 'upload'])->name('media.upload');
        Route::post('/media/folder', [MediaController::class, 'folder'])->name('media.folder');

        // Hotels
        Route::resource('hotels', HotelController::class);
        Route::post('hotels/{id}/restore', [HotelController::class, 'restore'])->name('hotels.restore');
        Route::resource('hotel-rooms', HotelRoomController::class)->except(['show']);
        Route::get('amenities', [AmenityController::class, 'index'])->name('amenities.index');
        Route::post('amenities', [AmenityController::class, 'store'])->name('amenities.store');
        Route::put('amenities/{amenity}', [AmenityController::class, 'update'])->name('amenities.update');
        Route::delete('amenities/{amenity}', [AmenityController::class, 'destroy'])->name('amenities.destroy');

        Route::get('services', [ServiceController::class, 'index'])->name('services.index');
        Route::post('services', [ServiceController::class, 'store'])->name('services.store');
        Route::put('services/{service}', [ServiceController::class, 'update'])->name('services.update');
        Route::delete('services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');

        Route::get('currencies', [CurrencyController::class, 'index'])->name('currencies.index');
        Route::post('currencies', [CurrencyController::class, 'store'])->name('currencies.store');
        Route::put('currencies/{currency}', [CurrencyController::class, 'update'])->name('currencies.update');
        Route::delete('currencies/{currency}', [CurrencyController::class, 'destroy'])->name('currencies.destroy');
    });
