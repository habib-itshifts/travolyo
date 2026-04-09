<?php

use App\Models\Currency;
use App\Models\TopDestination;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('test-load-hotel-details', [TestController::class , 'loadHotelDetails']);
Route::get('test-load-hotel', [TestController::class , 'loadHotels']);

Route::get('/hyperguest-test-b2b', function () {

    $url = 'https://hg-static.hyperguest.com/hotels.json'; // ⚠️ replace with real domain

    $response = Http::withHeaders([
        'Authorization' => 'Bearer 720c616825804c4498f1f21a1d128d4f',
        'Accept-Encoding' => 'gzip, deflate',
        'Accept' => 'application/json',
    ])->get($url, [
        'nights'   => 2,
        'guests'   => 2,
        'hotelIds' => 34326,
        'checkIn'  => now()->addDays(5)->format('Y-m-d'),
    ]);

    return response()->json([
        'status' => $response->status(),
        'body' => $response->body(),
        'json' => $response->json()
    ]);
});

Route::get('/hyperguest-test-b2b-hotel', function () {

    $url = 'https://hg-static.hyperguest.com/hotels.json'; // ⚠️ replace with real domain

    $response = Http::withHeaders([
        'Authorization' => 'Bearer 720c616825804c4498f1f21a1d128d4f',
        'Accept-Encoding' => 'gzip, deflate',
        'Accept' => 'application/json',
    ])->get($url);

    $hotelsList = collect($response->json());

    $destination = strtolower('Dubai');

    $destinationHotels = $hotelsList->filter(function ($h) use ($destination) {
            $city = strtolower($h['city'] ?? '');
            $country = strtolower($h['country'] ?? '');

            return $city=== $destination || $country === $destination;
        });

    return response()->json([
        'data' => $destinationHotels

    ]);
});



Route::get('/hyperguest-test-b2c', function () {

    $response = Http::withHeaders([
        'Authorization' => 'Bearer 415dc3e3dbb34fb9823a3c81a84356df',
        'Accept' => 'application/json',
        'Accept-Encoding' => 'gzip, deflate',
    ])->get('https://search-api.hyperguest.io/2.0/', [
        'nights'   => 2,
        'guests'   => 2,
        'hotelIds' => 34326,
        'checkIn'  => now()->addDays(5)->format('Y-m-d'),
    ]);

    return response()->json([
        'status' => $response->status(),
        'body'   => $response->body(),
    ]);
});

Route::resource('test', TestController::class);
Route::get('/', function () {
    $topCitiesFile = public_path('data/top-cities-to-book.json');
    $topCitiesConfig = [];
    $uaeDestinationCards = collect([
        [
            'city' => 'Dubai',
            'country' => 'UAE',
            'country_code' => 'AE',
            'location' => 'DXB',
            'image' => asset('assets/images/website/top-destination/dubai.png'),
            'image_alt' => 'Dubai skyline',
            'accommodations' => '19,464 accommodations',
        ],
        [
            'city' => 'Abu Dhabi',
            'country' => 'UAE',
            'country_code' => 'AE',
            'location' => 'AUH',
            'image' => asset('assets/images/website/top-destination/abu-dhabi.png'),
            'image_alt' => 'Abu Dhabi',
            'accommodations' => '721 accommodations',
        ],
        [
            'city' => 'Sharjah',
            'country' => 'UAE',
            'country_code' => 'AE',
            'location' => 'SHJ',
            'image' => asset('assets/images/website/top-destination/sharjah.png'),
            'image_alt' => 'Sharjah',
            'accommodations' => '323 accommodations',
        ],
        [
            'city' => 'Ras Al Khaimah',
            'country' => 'UAE',
            'country_code' => 'AE',
            'location' => 'RKT',
            'image' => asset('assets/images/website/top-destination/rasul-khema.png'),
            'image_alt' => 'Ras Al Khaimah',
            'accommodations' => '398 accommodations',
        ],
        [
            'city' => 'Ajman',
            'country' => 'UAE',
            'country_code' => 'AE',
            'location' => 'QAJ',
            'image' => asset('assets/images/website/top-destination/ajman.png'),
            'image_alt' => 'Ajman beach',
            'accommodations' => '264 accommodations',
        ],
        [
            'city' => 'Fujairah',
            'country' => 'UAE',
            'country_code' => 'AE',
            'location' => 'FJR',
            'image' => asset('assets/images/website/top-destination/fujairah.jpg'),
            'image_alt' => 'Fujairah',
            'accommodations' => '210 accommodations',
        ],
    ]);

    if (is_file($topCitiesFile)) {
        $decoded = json_decode((string) file_get_contents($topCitiesFile), true);
        $topCitiesConfig = is_array($decoded) ? $decoded : [];
    }

    if (Schema::hasTable('top_destinations')) {
        $managedDestinations = TopDestination::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('city')
            ->get()
            ->map(function (TopDestination $destination) {
                return [
                    'city' => $destination->city,
                    'country' => $destination->country,
                    'country_code' => $destination->country_code,
                    'location' => $destination->location,
                    'image' => $destination->image_url,
                    'image_alt' => $destination->image_alt ?: $destination->city,
                    'accommodations' => $destination->accommodations_label,
                ];
            });

        if ($managedDestinations->isNotEmpty()) {
            $uaeDestinationCards = $managedDestinations->values();
        }
    }

    return view('website.index', [
        'topCitiesConfig'      => $topCitiesConfig,
        'uaeDestinationCards'  => $uaeDestinationCards,
        'defaultHotelCheckIn'  => now()->addDays(4)->toDateString(),
        'defaultHotelCheckOut' => now()->addDays(8)->toDateString(),
    ]);
})->name('website');

Route::get('/destinations/{key}', [WebsiteController::class, 'exploreDestinationShow'])->name('destinations.show');
Route::get('/explore-destination-by-hotel', [WebsiteController::class, 'exploreDestinationPublicHotels'])->name('explore-destination-by-hotel');

Route::view('/about-us', 'website.about-us')->name('about');
Route::view('/contact-us', 'website.contact-us')->name('contact');
Route::view('/careers', 'website.careers')->name('careers');
Route::view('/help-center', 'website.help-center')->name('help');
Route::view('/faqs', 'website.faqs')->name('faqs');
Route::get('/press', [BlogController::class, 'press'])->name('press');
Route::view('/privacy-policy', 'website.privacy-policy')->name('privacy');
Route::view('/terms-of-services', 'website.terms-of-services')->name('terms');
Route::view('/cookies-policy', 'website.cookies-policy')->name('cookies');
Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');
Route::get('/blogs/{blog:slug}', [BlogController::class, 'show'])->name('blogs.show');

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
    if (array_key_exists($code, Currency::supported())) {
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
