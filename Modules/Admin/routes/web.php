<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AmenityController;
use Modules\Admin\Http\Controllers\ActivityController;
use Modules\Admin\Http\Controllers\BlogController;
use Modules\Admin\Http\Controllers\BlogCategoryController;
use Modules\Admin\Http\Controllers\BlogTagController;
use Modules\Admin\Http\Controllers\CurrencyController;
use Modules\Admin\Http\Controllers\AdminDashboardController;
use Modules\Admin\Http\Controllers\HotelController;
use Modules\Admin\Http\Controllers\HotelRoomController;
use Modules\Admin\Http\Controllers\HotelScrapingController;
use Modules\Admin\Http\Controllers\MediaController;
use Modules\Admin\Http\Controllers\ProfileController;
use Modules\Admin\Http\Controllers\ServiceController;
use Modules\Admin\Http\Controllers\CustomerController;
use Modules\Admin\Http\Controllers\DestinationController;
use Modules\Admin\Http\Controllers\VendorController;
use Modules\Admin\Http\Controllers\BookingController;
use Modules\Admin\Http\Controllers\HotelDealController;
use Modules\Admin\Http\Controllers\HotelDealSupplementController;
use Modules\Admin\Http\Controllers\PromoCodeController;
use Modules\Admin\Http\Controllers\RoomTypeController;
use Modules\Admin\Http\Controllers\VendorRequestController;
use Modules\Admin\Http\Controllers\AdminCrmController;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'admin'])
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/crm-dashboard', [AdminCrmController::class, 'index'])->name('crm-dashboard');

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
        Route::get('hotels/scrape', [HotelScrapingController::class, 'create'])->name('hotels.scraping.create');
        Route::post('hotels/scrape', [HotelScrapingController::class, 'store'])->name('hotels.scraping.store');
        Route::resource('hotels', HotelController::class);
        Route::post('hotels/{id}/restore', [HotelController::class, 'restore'])->name('hotels.restore');
        Route::resource('hotel-rooms', HotelRoomController::class)->except(['show']);

        // Room Types
        Route::resource('room-types', RoomTypeController::class)->except(['show']);

        // Hotel Deals (nested under hotels)
        Route::resource('hotels.deals', HotelDealController::class)->except(['show']);

        // Promo Codes
        Route::resource('promo-codes', PromoCodeController::class)->except(['show']);

        // Hotel Deal Supplements
        Route::resource('hotel-deal-supplements', HotelDealSupplementController::class)->except(['show']);
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
        Route::get('destinations', [DestinationController::class, 'index'])->name('destinations.index');
        Route::post('destinations', [DestinationController::class, 'store'])->name('destinations.store');
        Route::put('destinations/{destination}', [DestinationController::class, 'update'])->name('destinations.update');
        Route::delete('destinations/{destination}', [DestinationController::class, 'destroy'])->name('destinations.destroy');

        // Users — Vendors & Customers
        Route::resource('vendors', VendorController::class)->only(['index', 'show', 'edit', 'update']);
        Route::resource('customers', CustomerController::class)->only(['index', 'show', 'edit', 'update']);

        // Bookings
        Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');

        Route::resource('activities', ActivityController::class)->except(['show']);
        Route::resource('blogs', BlogController::class)->except(['show']);
        Route::resource('blog-categories', BlogCategoryController::class)->except(['show']);
        Route::resource('blog-tags', BlogTagController::class)->except(['show']);
    });
