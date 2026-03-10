<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\DashboardController;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'admin'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });
