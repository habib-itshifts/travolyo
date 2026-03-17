<?php

use Illuminate\Support\Facades\Route;
use Modules\Vendor\Http\Controllers\DashboardController;
use Modules\Vendor\Http\Controllers\ProfileController;
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
    });
