<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\MediaController;
use Modules\Vendor\Http\Controllers\ActivityController;
use Modules\Vendor\Http\Controllers\DashboardController;
use Modules\Vendor\Http\Controllers\HotelController;
use Modules\Vendor\Http\Controllers\HotelRoomController;
use Modules\Vendor\Http\Controllers\HotelScrapingController;
use Modules\Vendor\Http\Controllers\BookingController;
use Modules\Vendor\Http\Controllers\HotelDealController;
use Modules\Vendor\Http\Controllers\HotelDealSupplementController;
use Modules\Vendor\Http\Controllers\PromoCodeController;
use Modules\Vendor\Http\Controllers\RoomTypeController;
use Modules\Vendor\Http\Controllers\ProfileController;
use Modules\Vendor\Http\Controllers\SpaceController;
use Modules\Vendor\Http\Controllers\VendorDocumentController;

Route::prefix('vendor')
    ->name('vendor.')
    ->middleware(['auth', 'verified', 'vendor'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Documents
        Route::get('/documents', [VendorDocumentController::class, 'index'])->name('documents.index');
        Route::post('/documents', [VendorDocumentController::class, 'store'])->name('documents.store');
        Route::delete('/documents/{document}', [VendorDocumentController::class, 'destroy'])->name('documents.destroy');
        Route::post('/documents/submit', [VendorDocumentController::class, 'submit'])->name('documents.submit');

        // Profile
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

        // ─── Hotels ───────────────────────────────────────────────────────────
        // Vendor CRUD for hotels. Scoped to hotels owned by the authenticated
        // vendor (author_id = auth()->id()). Status is always forced to "draft"
        // by SaveHotelAction — admin must approve before a hotel goes live.
        Route::get('hotels/scrape', [HotelScrapingController::class, 'create'])->name('hotels.scraping.create');
        Route::post('hotels/scrape', [HotelScrapingController::class, 'store'])->name('hotels.scraping.store');
        Route::resource('hotels', HotelController::class);

        // Hotel Rooms — scoped to rooms of the vendor's own hotels.
        Route::resource('hotel-rooms', HotelRoomController::class)->except(['show']);

        // Room Types (scoped to vendor's own)
        Route::resource('room-types', RoomTypeController::class)->except(['show']);

        // Hotel Deals (nested under hotels, scoped to vendor's own)
        Route::resource('hotels.deals', HotelDealController::class)->except(['show']);

        // Promo Codes (scoped to vendor's own)
        Route::resource('promo-codes', PromoCodeController::class)->except(['show']);

        // Hotel Deal Supplements (scoped to vendor's own deals)
        Route::resource('hotel-deal-supplements', HotelDealSupplementController::class)->except(['show']);

        // ─── Spaces ─────────────────────────────────────────────────────────
        // Vendor CRUD for spaces. Scoped to spaces owned by the authenticated
        // vendor (author_id = auth()->id()). Status is always forced to "draft"
        // by SaveSpaceAction — admin must approve before a space goes live.
        Route::resource('spaces', SpaceController::class);
        Route::get('spaces/{id}/availability', [SpaceController::class, 'availability'])->name('spaces.availability');
        Route::post('spaces/{id}/availability', [SpaceController::class, 'updateAvailability'])->name('spaces.availability.update');

        // ─── Activities ───────────────────────────────────────────────────────
        // Vendor CRUD for activities. Scoped to activities owned by the authenticated
        // vendor (author_id = auth()->id()). Status is always forced to "pending"
        // by SaveActivityAction — admin must approve before an activity goes live.
        Route::resource('activities', ActivityController::class)->except(['show']);

        // Bookings
        Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');

        // ─── Media browser API ────────────────────────────────────────────────
        // Vendors need to pick images for their hotels and rooms. We reuse the
        // Admin MediaController (it has no admin-specific logic) but register it
        // under the vendor route prefix so vendor middleware applies.
        Route::get('/media/browser', [MediaController::class, 'browser'])->name('media.browser');
        Route::post('/media/upload',  [MediaController::class, 'upload'])->name('media.upload');
        Route::post('/media/folder',  [MediaController::class, 'folder'])->name('media.folder');
    });
