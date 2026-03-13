<?php

use Illuminate\Support\Facades\Route;
use Modules\Hotel\Http\Controllers\HotelController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('hotels', HotelController::class)->names('hotel');
});
