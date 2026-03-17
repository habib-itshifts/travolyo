<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AmenityController;
use Modules\Admin\Http\Controllers\ActivityController;
use Modules\Admin\Http\Controllers\BlogController;
use Modules\Admin\Http\Controllers\BlogCategoryController;
use Modules\Admin\Http\Controllers\BlogTagController;
use Modules\Admin\Http\Controllers\CurrencyController;
use Modules\Admin\Http\Controllers\DashboardController;
use Modules\Admin\Http\Controllers\HotelController;
use Modules\Admin\Http\Controllers\HotelRoomController;
use Modules\Admin\Http\Controllers\MediaController;
use Modules\Admin\Http\Controllers\ProfileController;
use Modules\Admin\Http\Controllers\ServiceController;
use Modules\Admin\Http\Controllers\VendorRequestController;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'admin'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Profile
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

        // Vendor Requests
        Route::get('vendor-requests', [VendorRequestController::class, 'index'])->name('vendor-requests.index');
        Route::post('vendor-requests/{user}/approve', [VendorRequestController::class, 'approve'])->name('vendor-requests.approve');
        Route::post('vendor-requests/{user}/reject', [VendorRequestController::class, 'reject'])->name('vendor-requests.reject');
        Route::get('vendor-requests/{user}/documents', [VendorRequestController::class, 'documents'])->name('vendor-requests.documents');
        Route::get('vendor-requests/documents/{document}/download', [VendorRequestController::class, 'downloadDocument'])->name('vendor-requests.documents.download');
        Route::post('vendor-requests/documents/{document}/approve', [VendorRequestController::class, 'approveDocument'])->name('vendor-requests.documents.approve');
        Route::post('vendor-requests/documents/{document}/reject', [VendorRequestController::class, 'rejectDocument'])->name('vendor-requests.documents.reject');
        Route::post('vendor-requests/{user}/verify', [VendorRequestController::class, 'verify'])->name('vendor-requests.verify');

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

        Route::resource('activities', ActivityController::class)->except(['show']);
        Route::resource('blogs', BlogController::class)->except(['show']);
        Route::resource('blog-categories', BlogCategoryController::class)->except(['show']);
        Route::resource('blog-tags', BlogTagController::class)->except(['show']);
    });
