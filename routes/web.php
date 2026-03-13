<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('website.index');
})->name('website');

Route::view('/about-us', 'website.about-us')->name('about');
Route::view('/contact-us', 'website.contact-us')->name('contact');
Route::view('/careers', 'website.careers')->name('careers');
Route::view('/help-center', 'website.help-center')->name('help');
Route::view('/faqs', 'website.faqs')->name('faqs');
Route::view('/privacy-policy', 'website.privacy-policy')->name('privacy');
Route::view('/terms-of-services', 'website.terms-of-services')->name('terms');
Route::view('/cookies-policy', 'website.cookies-policy')->name('cookies');

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

/*
|--------------------------------------------------------------------------
| Booking detail
|--------------------------------------------------------------------------
*/
Route::get('/bookings/{code}', [BookingController::class, 'show'])->name('bookings.show');

/*
|--------------------------------------------------------------------------
| Payment returns & webhooks
|--------------------------------------------------------------------------
*/
Route::prefix('payments')->name('payments.')->group(function () {
    // Stripe
    Route::get( '/stripe/return',   [PaymentController::class, 'stripeReturn'])  ->name('stripe.return');
    Route::get( '/stripe/cancel',   [PaymentController::class, 'stripeCancel'])  ->name('stripe.cancel');
    Route::post('/stripe/webhook',  [PaymentController::class, 'stripeWebhook']) ->name('stripe.webhook')
         ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

    // N-Genius
    Route::get( '/ngenius/return',  [PaymentController::class, 'ngeniusReturn']) ->name('ngenius.return');
    Route::post('/ngenius/webhook', [PaymentController::class, 'ngeniusWebhook'])->name('ngenius.webhook')
         ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
});

require __DIR__.'/auth.php';

// Admin, Vendor, Customer panel routes are handled by their nwidart modules:
// → Modules/Admin/routes/web.php    (prefix: /admin,    name: admin.*)
// → Modules/Vendor/routes/web.php   (prefix: /vendor,   name: vendor.*)
// → Modules/Customer/routes/web.php (prefix: /customer, name: customer.*)
