<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Breeze default)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Locale switcher — GET /locale/{locale}
|--------------------------------------------------------------------------
*/
Route::get('/locale/{locale}', function (string $locale) {
    if (array_key_exists($locale, config('language.supported', []))) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('locale.switch');

/*
|--------------------------------------------------------------------------
| Currency switcher — GET /currency/{code}
|--------------------------------------------------------------------------
*/
Route::get('/currency/{code}', function (string $code) {
    $code = strtoupper($code);
    if (array_key_exists($code, config('currency.supported', []))) {
        session(['currency' => $code]);
    }
    return redirect()->back();
})->name('currency.switch');

require __DIR__.'/auth.php';

// Admin, Vendor, Customer panel routes are handled by their nwidart modules:
// → Modules/Admin/routes/web.php    (prefix: /admin,    name: admin.*)
// → Modules/Vendor/routes/web.php   (prefix: /vendor,   name: vendor.*)
// → Modules/Customer/routes/web.php (prefix: /customer, name: customer.*)
