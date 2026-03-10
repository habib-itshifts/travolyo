# 🌍 Travel Booking Platform — Project Blueprint
> Booking.com / Agoda style platform with multi-vendor, multi-language, multi-currency support.
> Built with Laravel (Modular Architecture via `nwidart/laravel-modules`)

---

## 📑 Table of Contents
1. [Project Overview](#project-overview)
2. [User Roles](#user-roles)
3. [Core Features](#core-features)
4. [Architecture Decision — Modular](#architecture-decision--modular)
5. [Module Map](#module-map)
6. [Full File Structure](#full-file-structure)
7. [Listing Sources & Providers](#listing-sources--providers)
8. [Search & Filter Architecture](#search--filter-architecture)
9. [Multi-Language](#multi-language)
10. [Multi-Currency](#multi-currency)
11. [Payment Gateways](#payment-gateways)
12. [Commission & Payout Model](#commission--payout-model)
13. [Key Packages](#key-packages)
14. [Environment Variables](#environment-variables)
15. [Database — High Level](#database--high-level)
16. [Development Phases](#development-phases)

---

## Project Overview

A full-featured online travel agency (OTA) platform where:
- **Customers** search, compare and book Hotels, Flights and Activities
- **Vendors** register, get approved and upload their own listings
- **Admins** approve vendors, approve listings, manage commissions and payouts
- **External APIs** (Amadeus, Duffel, XML providers) supply additional listings that are merged with local vendor listings into one unified search result
- Platform collects **full payment from customer**, deducts commission, pays vendor after checkout

---

## User Roles

| Role | Description |
|---|---|
| `admin` | Full platform control. Approves vendors, listings, manages commissions & payouts |
| `vendor` | Registers on platform, gets approved, uploads Hotels/Flights/Activities |
| `customer` | Searches, books, pays, reviews |

---

## Core Features

### Vendor Flow
- Vendor registers → status: `pending`
- Admin reviews & approves → status: `approved`
- Vendor can now upload listings (Hotels, Flights, Activities)
- Each listing goes through approval → status: `pending_approval` → `approved` → visible on site
- Rejected listings get a rejection reason

### Booking Flow
- Customer searches (unified results from local DB + all external APIs)
- Customer selects listing → views detail → proceeds to checkout
- Platform collects full payment
- Booking confirmed → notifications sent to customer + vendor
- After checkout + safety delay → commission deducted → vendor payout triggered

### Commission Model
- Per vendor or per listing type
- Type: `fixed` (e.g. $10 flat) or `percentage` (e.g. 15%)
- Example: Customer pays $100 → 15% commission = $15 → Vendor gets $85
- Payout happens after stay/activity completion + configurable safety delay (e.g. 48 hours)

---

## Architecture Decision — Modular

**Package:** `nwidart/laravel-modules`

### Why Modular?
- Each domain (Hotel, Flight, Payment, etc.) is fully self-contained
- External API integrations are isolated — Amadeus changes don't affect Duffel
- Each payment gateway is a self-contained class
- Easy to add new modules (new provider, new gateway) without touching existing code
- Clean separation of Admin / Vendor / Customer concerns
- Each module owns its own: routes, controllers, models, migrations, services

---

## Module Map

```
Modules/
├── Core              # Shared DTOs, Contracts/Interfaces, Enums, Helpers
├── Auth              # Login, Register, Social Auth
├── Admin             # Admin panel — approvals, commissions, reports
├── Vendor            # Vendor dashboard, listing management
├── Customer          # Customer dashboard, booking history
├── Hotel             # Hotel listings, search, filters
├── Flight            # Flight listings, search, filters
├── Activity          # Activity/experience listings
├── Booking           # Full booking lifecycle
├── Payment           # Gateway orchestration, webhooks
├── Payout            # Vendor payout management
├── Commission        # Commission rules & calculation
├── Currency          # Multi-currency via torann/laravel-currency
├── Localization      # Multi-language, locale middleware
├── Review            # Customer reviews & ratings
├── Notification      # All platform notifications
└── Providers/        # External API integrations (not a module, lives in app/)
    ├── Amadeus/
    ├── Duffel/
    └── XmlProviders/
```

---

## Full File Structure

```
Modules/
│
├── Core/
│   ├── Contracts/
│   │   ├── HotelSearchInterface.php
│   │   ├── FlightSearchInterface.php
│   │   ├── ActivitySearchInterface.php
│   │   ├── BookingProviderInterface.php
│   │   └── PaymentGatewayInterface.php
│   ├── DTOs/
│   │   ├── HotelResultDTO.php
│   │   ├── FlightResultDTO.php
│   │   └── ActivityResultDTO.php
│   ├── Enums/
│   │   ├── BookingStatus.php       # pending, confirmed, cancelled, completed
│   │   ├── ListingStatus.php       # draft, pending_approval, approved, rejected
│   │   ├── ListingType.php         # hotel, flight, activity
│   │   ├── PaymentStatus.php       # pending, paid, failed, refunded
│   │   ├── PayoutStatus.php        # pending, processing, paid, failed
│   │   ├── CommissionType.php      # fixed, percentage
│   │   └── UserRole.php            # admin, vendor, customer
│   ├── Helpers/
│   │   └── MoneyHelper.php
│   └── Providers/
│       └── CoreServiceProvider.php
│
├── Auth/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── LoginController.php
│   │   │   ├── RegisterController.php
│   │   │   └── SocialAuthController.php
│   │   └── Requests/
│   │       ├── LoginRequest.php
│   │       └── RegisterRequest.php
│   ├── Models/
│   │   └── User.php
│   ├── Services/
│   │   └── RegistrationService.php
│   ├── routes/web.php
│   └── Providers/AuthServiceProvider.php
│
├── Admin/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php
│   │   ├── VendorApprovalController.php
│   │   ├── ListingApprovalController.php
│   │   ├── CommissionController.php
│   │   ├── PayoutManagementController.php
│   │   ├── BookingManagementController.php
│   │   └── ReportController.php
│   ├── Services/
│   ├── routes/web.php
│   └── Providers/AdminServiceProvider.php
│
├── Vendor/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php
│   │   ├── ProfileController.php
│   │   ├── HotelController.php
│   │   ├── FlightController.php
│   │   ├── ActivityController.php
│   │   └── PayoutController.php
│   ├── Models/
│   │   └── VendorProfile.php
│   ├── Services/
│   │   ├── VendorApprovalService.php
│   │   └── VendorOnboardingService.php
│   ├── routes/web.php
│   └── Providers/VendorServiceProvider.php
│
├── Customer/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php
│   │   ├── ProfileController.php
│   │   └── BookingHistoryController.php
│   ├── routes/web.php
│   └── Providers/CustomerServiceProvider.php
│
├── Hotel/
│   ├── Http/Controllers/
│   │   ├── HotelSearchController.php
│   │   ├── HotelListingController.php
│   │   └── HotelDetailController.php
│   ├── Models/
│   │   ├── Hotel.php
│   │   ├── HotelRoom.php
│   │   └── HotelTranslation.php
│   ├── Services/
│   │   ├── HotelSearchService.php       # fans out to all providers concurrently
│   │   ├── HotelFilterService.php
│   │   └── HotelAggregatorService.php
│   ├── Filters/
│   │   ├── PriceRangeFilter.php
│   │   ├── StarRatingFilter.php
│   │   ├── AmenitiesFilter.php
│   │   ├── LocationFilter.php
│   │   ├── ReviewScoreFilter.php
│   │   ├── RefundableOnlyFilter.php
│   │   └── RoomTypeFilter.php
│   ├── database/migrations/
│   ├── routes/web.php
│   └── Providers/HotelServiceProvider.php
│
├── Flight/
│   ├── Http/Controllers/
│   │   ├── FlightSearchController.php
│   │   └── FlightDetailController.php
│   ├── Models/
│   │   ├── Flight.php
│   │   └── FlightSegment.php
│   ├── Services/
│   │   ├── FlightSearchService.php
│   │   └── FlightFilterService.php
│   ├── Filters/
│   │   ├── PriceRangeFilter.php
│   │   ├── StopsFilter.php              # non-stop, 1 stop, 2+
│   │   ├── AirlineFilter.php
│   │   ├── DepartureTimeFilter.php      # morning, afternoon, evening
│   │   ├── DurationFilter.php
│   │   ├── CabinClassFilter.php         # economy, business, first
│   │   ├── BaggageFilter.php
│   │   └── RefundableOnlyFilter.php
│   ├── database/migrations/
│   ├── routes/web.php
│   └── Providers/FlightServiceProvider.php
│
├── Activity/
│   ├── Http/Controllers/
│   │   ├── ActivitySearchController.php
│   │   └── ActivityDetailController.php
│   ├── Models/
│   │   ├── Activity.php
│   │   └── ActivityTranslation.php
│   ├── Services/
│   │   ├── ActivitySearchService.php
│   │   └── ActivityFilterService.php
│   ├── Filters/
│   │   ├── PriceRangeFilter.php
│   │   ├── CategoryFilter.php           # adventure, cultural, family
│   │   ├── DurationFilter.php           # half day, full day, multi-day
│   │   ├── RatingFilter.php
│   │   └── LanguageFilter.php
│   ├── database/migrations/
│   ├── routes/web.php
│   └── Providers/ActivityServiceProvider.php
│
├── Booking/
│   ├── Http/Controllers/
│   │   ├── CheckoutController.php
│   │   ├── BookingController.php
│   │   └── CancellationController.php
│   ├── Models/
│   │   ├── Booking.php
│   │   ├── BookingItem.php
│   │   └── BookingPassenger.php
│   ├── Services/
│   │   ├── BookingService.php
│   │   ├── CancellationService.php
│   │   └── BookingConfirmationService.php
│   ├── Jobs/
│   │   ├── SendBookingConfirmation.php
│   │   └── HandleRefundRequest.php
│   ├── database/migrations/
│   ├── routes/web.php
│   └── Providers/BookingServiceProvider.php
│
├── Payment/
│   ├── Http/Controllers/
│   │   ├── PaymentController.php
│   │   └── WebhookController.php        # single entry point for all gateway webhooks
│   ├── Models/
│   │   ├── Payment.php
│   │   ├── Refund.php
│   │   └── PaymentMethod.php
│   ├── Services/
│   │   ├── PaymentService.php           # orchestrator — resolves correct gateway
│   │   └── RefundService.php
│   ├── Gateways/
│   │   ├── Stripe/
│   │   │   ├── StripeGateway.php        # implements PaymentGatewayInterface
│   │   │   ├── StripeWebhookHandler.php
│   │   │   └── DTOs/StripePaymentDTO.php
│   │   ├── PayPal/
│   │   │   ├── PayPalGateway.php
│   │   │   ├── PayPalWebhookHandler.php
│   │   │   └── DTOs/PayPalPaymentDTO.php
│   │   ├── Tap/
│   │   │   ├── TapGateway.php           # Middle East
│   │   │   ├── TapWebhookHandler.php
│   │   │   └── DTOs/TapPaymentDTO.php
│   │   ├── HyperPay/
│   │   │   ├── HyperPayGateway.php      # Saudi / UAE
│   │   │   ├── HyperPayWebhookHandler.php
│   │   │   └── DTOs/HyperPayPaymentDTO.php
│   │   └── Moyasar/
│   │       ├── MoyasarGateway.php       # Saudi market
│   │       ├── MoyasarWebhookHandler.php
│   │       └── DTOs/MoyasarPaymentDTO.php
│   ├── Jobs/
│   │   └── ProcessPaymentWebhook.php
│   ├── database/migrations/
│   ├── routes/api.php                   # POST /webhooks/{gateway}
│   └── Providers/PaymentServiceProvider.php
│
├── Payout/
│   ├── Models/
│   │   └── Payout.php
│   ├── Services/
│   │   └── PayoutService.php
│   ├── Jobs/
│   │   └── ProcessVendorPayout.php
│   ├── database/migrations/
│   └── Providers/PayoutServiceProvider.php
│
├── Commission/
│   ├── Models/
│   │   └── Commission.php
│   ├── Services/
│   │   ├── CommissionService.php
│   │   └── CommissionCalculatorService.php
│   ├── database/migrations/
│   └── Providers/CommissionServiceProvider.php
│
├── Currency/
│   ├── Services/
│   │   └── CurrencyService.php          # thin wrapper around torann/laravel-currency
│   ├── Http/Middleware/
│   │   └── CurrencyMiddleware.php
│   └── Providers/CurrencyServiceProvider.php
│
├── Localization/
│   ├── Http/Middleware/
│   │   └── LocaleMiddleware.php
│   ├── Services/
│   │   └── TranslationService.php
│   └── Providers/LocalizationServiceProvider.php
│
├── Review/
│   ├── Http/Controllers/
│   │   └── ReviewController.php
│   ├── Models/
│   │   └── Review.php
│   ├── database/migrations/
│   └── Providers/ReviewServiceProvider.php
│
└── Notification/
    ├── Notifications/
    │   ├── BookingConfirmed.php
    │   ├── BookingCancelled.php
    │   ├── VendorApproved.php
    │   ├── VendorRejected.php
    │   ├── ListingApproved.php
    │   ├── ListingRejected.php
    │   └── PayoutProcessed.php
    └── Providers/NotificationServiceProvider.php

app/
└── Providers/
    ├── Amadeus/
    │   ├── AmadeusClient.php                    # OAuth2 auth, HTTP client, token refresh
    │   ├── Hotels/
    │   │   ├── AmadeusHotelSearchApi.php         # search availability
    │   │   ├── AmadeusHotelDetailApi.php         # single hotel details
    │   │   ├── AmadeusHotelPricingApi.php        # live price check
    │   │   └── AmadeusHotelBookingApi.php        # create booking
    │   ├── Flights/
    │   │   ├── AmadeusFlightSearchApi.php        # search offers
    │   │   ├── AmadeusFlightPricingApi.php       # confirm price before booking
    │   │   ├── AmadeusFlightBookingApi.php       # create order
    │   │   └── AmadeusFlightStatusApi.php        # flight status check
    │   ├── Activities/
    │   │   ├── AmadeusActivitySearchApi.php
    │   │   └── AmadeusActivityDetailApi.php
    │   ├── DTOs/
    │   │   ├── AmadeusHotelDTO.php
    │   │   ├── AmadeusFlightDTO.php
    │   │   └── AmadeusActivityDTO.php
    │   └── AmadeusServiceProvider.php            # binds client (singleton) + all Api classes
    │
    ├── Duffel/
    │   ├── DuffelClient.php                      # API key auth, HTTP client
    │   ├── Flights/
    │   │   ├── DuffelFlightSearchApi.php         # offer requests
    │   │   ├── DuffelFlightPricingApi.php        # offer pricing
    │   │   ├── DuffelFlightBookingApi.php        # create order
    │   │   ├── DuffelFlightSeatMapApi.php        # seat selection
    │   │   └── DuffelFlightCancellationApi.php   # cancel order
    │   ├── DTOs/
    │   │   ├── DuffelFlightOfferDTO.php
    │   │   └── DuffelFlightOrderDTO.php
    │   └── DuffelServiceProvider.php
    │
    └── XmlProviders/
        ├── BaseXmlClient.php                     # shared SOAP envelope, XML parsing
        ├── ProviderA/
        │   ├── ProviderAClient.php
        │   ├── Hotels/
        │   │   ├── ProviderAHotelSearchApi.php
        │   │   ├── ProviderAHotelDetailApi.php
        │   │   └── ProviderAHotelBookingApi.php
        │   ├── Flights/
        │   │   ├── ProviderAFlightSearchApi.php
        │   │   └── ProviderAFlightBookingApi.php
        │   └── DTOs/
        │       ├── ProviderAHotelDTO.php
        │       └── ProviderAFlightDTO.php
        └── ProviderB/
            ├── ProviderBClient.php
            ├── Activities/
            │   ├── ProviderBActivitySearchApi.php
            │   ├── ProviderBActivityDetailApi.php
            │   └── ProviderBActivityBookingApi.php
            └── DTOs/
                └── ProviderBActivityDTO.php
```

---

## Listing Sources & Providers

### 4 Sources of Listings

| Source | Type | Auth | Notes |
|---|---|---|---|
| Local Vendors | MySQL DB | — | Uploaded & approved via admin panel |
| Amadeus | REST API | OAuth2 | Hotels + Flights + Activities |
| Duffel | REST API | API Key | Flights only (offers + orders) |
| XML Providers | SOAP/XML | Varies | Hotels, Flights, Activities |

### API Class Naming Convention

Every external API class follows: `{Provider}{ListingType}{Action}Api.php`

| Class | Responsibility |
|---|---|
| `AmadeusHotelSearchApi` | Search hotel availability |
| `AmadeusHotelDetailApi` | Fetch single hotel details |
| `AmadeusHotelPricingApi` | Live price check before booking |
| `AmadeusHotelBookingApi` | Create hotel booking |
| `AmadeusFlightSearchApi` | Search flight offers |
| `AmadeusFlightPricingApi` | Confirm price before booking |
| `AmadeusFlightBookingApi` | Create flight order |
| `AmadeusFlightStatusApi` | Check flight status |
| `AmadeusActivitySearchApi` | Search activities |
| `AmadeusActivityDetailApi` | Fetch activity details |
| `DuffelFlightSearchApi` | Duffel offer requests |
| `DuffelFlightPricingApi` | Duffel offer pricing |
| `DuffelFlightBookingApi` | Duffel create order |
| `DuffelFlightSeatMapApi` | Duffel seat selection |
| `DuffelFlightCancellationApi` | Duffel cancel order |
| `ProviderAHotelSearchApi` | XML Provider A hotel search |
| `ProviderAFlightSearchApi` | XML Provider A flight search |
| `ProviderBActivitySearchApi` | XML Provider B activity search |

Each Api class: injected with shared provider Client, does **one thing only**, normalizes response into unified DTO, keeps `rawData` for booking step.

### Aggregation Flow

```
Customer searches
      ↓
SearchService fans out CONCURRENTLY to all sources:
   ├── LocalProvider     → queries DB
   ├── AmadeusProvider   → calls Amadeus API
   ├── DuffelProvider    → calls Duffel API
   └── XmlProvider       → calls XML/SOAP API
      ↓
Each returns HotelResultDTO[] / FlightResultDTO[] (normalized)
      ↓
AggregatorService merges all into one collection
      ↓
FilterService applies filters (Pipeline pattern)
      ↓
SortService sorts results
      ↓
Paginated response → Controller → View
```

### Unified DTO (example: Hotel)

```php
class HotelResultDTO {
    string  $source          // 'local' | 'amadeus' | 'duffel' | 'provider_a'
    string  $externalId      // source system ID
    string  $name
    string  $description
    string  $city
    string  $country
    float   $latitude
    float   $longitude
    int     $starRating       // 1–5
    float   $pricePerNight   // always in USD
    string  $currency
    array   $amenities
    array   $images
    float   $reviewScore
    int     $reviewCount
    bool    $isRefundable
    ?string $cancellationPolicy
    array   $roomTypes
    array   $rawData          // original payload — used for booking API call
}
```

> ⚠️ **`rawData` is critical.** When booking, you must send provider-original format back. Never use normalized DTO for booking calls.

### Caching Strategy

| Data | Cache Duration |
|---|---|
| Search results | 15 minutes (keyed by search params hash) |
| Listing detail | 1 hour |
| Availability | 5 minutes |
| Price at checkout | **Never cache** — always live |

---

## Search & Filter Architecture

Filters use Laravel's **Pipeline pattern**. Applied after aggregation on the unified DTO collection — this enables cross-provider filtering.

### Hotel Filters
`PriceRange` · `StarRating` · `Amenities` · `Location (radius)` · `ReviewScore` · `RefundableOnly` · `RoomType` · `Provider`

### Flight Filters
`PriceRange` · `Stops` · `Airline` · `DepartureTime` · `Duration` · `CabinClass` · `Baggage` · `RefundableOnly`

### Activity Filters
`PriceRange` · `Category` · `Duration` · `Rating` · `Language`

### Search Query Example
```
GET /en/search/hotels?
  city=Dubai
  &check_in=2026-04-10
  &check_out=2026-04-15
  &adults=2
  &price_min=50&price_max=300
  &stars[]=4&stars[]=5
  &amenities[]=pool&amenities[]=wifi
  &refundable=true
  &sort=price_asc
  &page=1
```

---

## Multi-Language

**Package:** `spatie/laravel-translatable`

### Two Content Types

| Type | Storage | Method |
|---|---|---|
| Static UI (buttons, labels) | `lang/` files | `__('key')` helper |
| Dynamic content (hotel names, descriptions) | DB translation tables | Spatie translatable |

### Supported Languages (configurable in `config/languages.php`)
`en` · `ar` · `fr` · `de` · `zh` + easily extendable

### URL Strategy
```
yourdomain.com/en/hotels/dubai
yourdomain.com/ar/hotels/dubai
yourdomain.com/fr/hotels/dubai
```

### RTL Support
Handled at layout level via `dir` attribute:
```html
<html lang="{{ app()->getLocale() }}"
      dir="{{ in_array(app()->getLocale(), ['ar', 'ur', 'he']) ? 'rtl' : 'ltr' }}">
```

### Translation Tables
```
hotel_translations      (hotel_id, locale, name, description, address, policies)
activity_translations   (activity_id, locale, name, description, includes, excludes)
room_type_translations  (room_type_id, locale, name, description)
amenity_translations    (amenity_id, locale, name)
```

### Locale Detection Priority
1. URL prefix (`/ar/`)
2. Auth user preference (`users.locale`)
3. Browser `Accept-Language` header
4. Fallback: `config('app.fallback_locale')` = `en`

---

## Multi-Currency

**Package:** `torann/laravel-currency`

### Key Rules
- ✅ All prices stored in **USD** in the database
- ✅ Conversion happens at display time via package
- ✅ Exchange rate **snapshotted at booking time** (stored in `bookings.exchange_rate`)
- ✅ Rates refreshed daily via `php artisan currency:update` (scheduled)
- ❌ No custom `currency_rates` table — package manages its own `currencies` table

### Currency Detection Priority
1. Query param `?currency=AED`
2. Session `currency`
3. Auth user preference (`users.currency`)
4. GeoIP detection
5. Default: `USD`

### Supported Currencies (configurable in `config/currencies.php`)
`USD` · `AED` · `EUR` · `GBP` · `SAR` · `INR` · `CNY` + extendable

### Blade Usage
```blade
{{ currency($hotel->base_price_usd) }}
{{-- Output: AED 551.00 (based on active currency) --}}
```

### Booking Snapshot
```php
// Stored at booking creation — never recalculated
'base_amount_usd'  => $amountUsd,
'charged_amount'   => $currencyService->convert($amountUsd, $userCurrency),
'charged_currency' => $userCurrency,
'exchange_rate'    => $currencyService->getRate('USD', $userCurrency),
```

---

## Payment Gateways

**Contract:** `Modules/Core/Contracts/PaymentGatewayInterface.php`

Every gateway implements:
```php
interface PaymentGatewayInterface {
    charge(PaymentRequestDTO): PaymentResultDTO
    refund(string $txId, float $amount): RefundResultDTO
    verify(string $txId): PaymentResultDTO
    handleWebhook(Request): void
    supportsPartialRefund(): bool
    supportedCurrencies(): array
}
```

### Active Gateways

| Gateway | Market | Notes |
|---|---|---|
| Stripe | Global | Cards, wallets |
| PayPal | Global | PayPal balance + cards |
| Tap | Middle East | Kuwait, UAE, Saudi, Bahrain |
| HyperPay | Saudi / UAE | Mada, Visa, Mastercard |
| Moyasar | Saudi Arabia | Mada + cards |

### Enable/Disable via `.env`
```env
ACTIVE_GATEWAYS=stripe,tap,moyasar
```

### Webhook Entry Point
All gateways POST to one controller:
```
POST /webhooks/stripe
POST /webhooks/tap
POST /webhooks/moyasar
```
`WebhookController` routes to the correct `{Gateway}WebhookHandler` → dispatches `ProcessPaymentWebhook` job.

---

## Commission & Payout Model

### Commission Rules
- Set per vendor or per listing type
- Types: `fixed` (flat amount) or `percentage` (% of total)
- Configurable per market / vendor tier

### Example
```
Customer pays:       $100.00
Commission (15%):  - $15.00
Vendor payout:       $85.00
```

### Payout Timing
```
Booking completed (checkout date passed)
      ↓
Safety delay: configurable (default 48 hours)
      ↓
ProcessVendorPayout job fires
      ↓
Commission deducted
      ↓
Remaining amount transferred to vendor bank/wallet
```

### Payout Statuses
`pending` → `processing` → `paid` / `failed`

---

## Key Packages

| Package | Purpose |
|---|---|
| `nwidart/laravel-modules` | Modular architecture |
| `spatie/laravel-permission` | Role & permission management |
| `spatie/laravel-translatable` | DB model translations |
| `torann/laravel-currency` | Currency conversion + formatting |
| `laravel/socialite` | Social login (Google, Facebook) |
| `spatie/laravel-medialibrary` | Image/file uploads for listings |
| `spatie/laravel-activitylog` | Admin audit trail |
| `laravel/horizon` | Queue monitoring |
| `barryvdh/laravel-debugbar` | Dev debugging |

---

## Environment Variables

```env
# App
APP_NAME="Travel Booking Platform"
APP_URL=https://yourdomain.com
APP_FALLBACK_LOCALE=en

# External Providers
AMADEUS_CLIENT_ID=
AMADEUS_CLIENT_SECRET=
AMADEUS_BASE_URL=https://test.api.amadeus.com   # change to production

DUFFEL_ACCESS_TOKEN=
DUFFEL_BASE_URL=https://api.duffel.com

PROVIDER_A_URL=
PROVIDER_A_USERNAME=
PROVIDER_A_PASSWORD=

PROVIDER_B_URL=
PROVIDER_B_API_KEY=

# Payment Gateways
ACTIVE_GATEWAYS=stripe,tap

STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=

PAYPAL_CLIENT_ID=
PAYPAL_SECRET=
PAYPAL_MODE=sandbox

TAP_SECRET_KEY=
TAP_PUBLIC_KEY=

HYPERPAY_ACCESS_TOKEN=
HYPERPAY_ENTITY_ID=
HYPERPAY_MODE=test

MOYASAR_SECRET_KEY=

# Currency
OPENEXCHANGERATES_APP_ID=
# or
FIXER_API_KEY=

# Payout
VENDOR_PAYOUT_SAFETY_DELAY_HOURS=48
```

---

## Database — High Level

> Full schema with migrations to be documented separately.

### Core Tables

```
users                    (id, name, email, password, role, locale, currency, status)
vendor_profiles          (id, user_id, company_name, status, approved_at, rejection_reason, ...)
commissions              (id, vendor_id, listing_type, type, value, ...)

hotels                   (id, vendor_id, source, external_id, stars, base_price_usd, status, ...)
hotel_translations       (id, hotel_id, locale, name, description, address, policies)
hotel_rooms              (id, hotel_id, base_price_usd, capacity, ...)
room_type_translations   (id, room_type_id, locale, name, description)

flights                  (id, vendor_id, source, external_id, base_price_usd, status, ...)
flight_segments          (id, flight_id, origin, destination, departs_at, arrives_at, ...)

activities               (id, vendor_id, source, external_id, base_price_usd, status, ...)
activity_translations    (id, activity_id, locale, name, description, includes, excludes)

bookings                 (id, user_id, status, base_amount_usd, charged_amount, charged_currency, exchange_rate, ...)
booking_items            (id, booking_id, bookable_type, bookable_id, source, raw_data, ...)
booking_passengers       (id, booking_id, type, first_name, last_name, passport_no, ...)

payments                 (id, booking_id, gateway, transaction_id, amount, currency, status, ...)
refunds                  (id, payment_id, amount, reason, status, ...)
payouts                  (id, vendor_id, booking_id, amount_usd, commission_usd, status, paid_at, ...)

reviews                  (id, user_id, reviewable_type, reviewable_id, rating, comment, ...)

currencies               (managed by torann/laravel-currency package)
```

---

## Development Phases

### Phase 1 — Foundation
- [ ] Install & configure `nwidart/laravel-modules`
- [ ] Set up Core module (DTOs, Contracts, Enums)
- [ ] Auth module (register, login, roles via spatie/permission)
- [ ] Admin, Vendor, Customer modules (basic dashboards)
- [ ] Database migrations for all core tables

### Phase 2 — Listings
- [ ] Hotel module (local vendor upload + approval flow)
- [ ] Flight module (local vendor upload + approval flow)
- [ ] Activity module (local vendor upload + approval flow)
- [ ] Media uploads via spatie/laravel-medialibrary
- [ ] Translation support via spatie/laravel-translatable

### Phase 3 — External Providers
- [ ] Amadeus integration (hotels + flights)
- [ ] Duffel integration (flights)
- [ ] XML Provider A integration
- [ ] XML Provider B integration
- [ ] AggregatorService + unified search

### Phase 4 — Search & Filters
- [ ] Hotel search + filter pipeline
- [ ] Flight search + filter pipeline
- [ ] Activity search + filter pipeline
- [ ] Caching layer for search results

### Phase 5 — Booking & Payment
- [ ] Checkout flow
- [ ] Stripe gateway
- [ ] Additional gateways (Tap, PayPal, HyperPay, Moyasar)
- [ ] Webhook handling
- [ ] Booking confirmation notifications

### Phase 6 — Commission & Payouts
- [ ] Commission rules engine
- [ ] Payout scheduling (post-checkout + safety delay)
- [ ] Vendor payout dashboard

### Phase 7 — Multi-lang & Multi-currency
- [ ] torann/laravel-currency setup + scheduler
- [ ] Locale middleware + URL prefix routing
- [ ] RTL layout support
- [ ] Translation management in admin panel

### Phase 8 — Polish
- [ ] Reviews & ratings
- [ ] Admin reports & analytics
- [ ] Activity log (spatie/laravel-activitylog)
- [ ] Laravel Horizon for queue monitoring
- [ ] Performance optimization & load testing

---

> **Last Updated:** March 2026
> **Stack:** Laravel 11 · PHP 8.3 · MySQL 8 · Redis · Laravel Horizon · Modular (nwidart)


## 🎨 Layout Structure

Each role has its own completely separate layout. No shared layout between Admin, Vendor, Customer and Public.

---

### Folder Structure

```
resources/
└── views/
    │
    ├── layouts/
    │   ├── public.blade.php            # Public website (home, search, listing pages)
    │   ├── admin.blade.php             # Admin panel layout
    │   ├── vendor.blade.php            # Vendor dashboard layout
    │   ├── customer.blade.php          # Customer dashboard layout
    │   └── auth.blade.php             # Login / Register pages (no sidebar)
    │
    ├── components/
    │   ├── public/
    │   │   ├── navbar.blade.php        # Public top nav (logo, search, login, currency, lang switcher)
    │   │   ├── footer.blade.php        # Public footer (links, socials, app download)
    │   │   ├── search-bar.blade.php    # Unified search bar (hotels/flights/activities tabs)
    │   │   └── currency-switcher.blade.php
    │   │
    │   ├── admin/
    │   │   ├── sidebar.blade.php       # Admin sidebar (vendor approvals, listings, reports)
    │   │   ├── topbar.blade.php        # Admin topbar (notifications, profile)
    │   │   └── stat-card.blade.php     # Reusable stats widget
    │   │
    │   ├── vendor/
    │   │   ├── sidebar.blade.php       # Vendor sidebar (listings, bookings, payouts)
    │   │   ├── topbar.blade.php        # Vendor topbar (notifications, profile)
    │   │   └── stat-card.blade.php
    │   │
    │   └── customer/
    │       ├── sidebar.blade.php       # Customer sidebar (bookings, wishlist, profile)
    │       └── topbar.blade.php        # Customer topbar (notifications, currency, lang)
    │
    ├── public/                         # Public-facing pages
    │   ├── home.blade.php
    │   ├── hotels/
    │   │   ├── index.blade.php         # Search results
    │   │   └── show.blade.php          # Hotel detail page
    │   ├── flights/
    │   │   ├── index.blade.php
    │   │   └── show.blade.php
    │   ├── activities/
    │   │   ├── index.blade.php
    │   │   └── show.blade.php
    │   └── checkout/
    │       ├── index.blade.php         # Checkout page
    │       └── confirmation.blade.php  # Booking confirmation
    │
    ├── admin/                          # Admin panel pages
    │   ├── dashboard.blade.php
    │   ├── vendors/
    │   │   ├── index.blade.php         # Vendor list + approval queue
    │   │   └── show.blade.php          # Vendor detail + approve/reject
    │   ├── listings/
    │   │   ├── index.blade.php         # All listings pending approval
    │   │   └── show.blade.php
    │   ├── bookings/
    │   │   └── index.blade.php
    │   ├── commissions/
    │   │   └── index.blade.php
    │   ├── payouts/
    │   │   └── index.blade.php
    │   └── reports/
    │       └── index.blade.php
    │
    ├── vendor/                         # Vendor panel pages
    │   ├── dashboard.blade.php
    │   ├── profile.blade.php
    │   ├── hotels/
    │   │   ├── index.blade.php
    │   │   ├── create.blade.php
    │   │   └── edit.blade.php
    │   ├── flights/
    │   │   ├── index.blade.php
    │   │   ├── create.blade.php
    │   │   └── edit.blade.php
    │   ├── activities/
    │   │   ├── index.blade.php
    │   │   ├── create.blade.php
    │   │   └── edit.blade.php
    │   ├── bookings/
    │   │   └── index.blade.php
    │   └── payouts/
    │       └── index.blade.php
    │
    ├── customer/                       # Customer panel pages
    │   ├── dashboard.blade.php
    │   ├── profile.blade.php
    │   ├── bookings/
    │   │   ├── index.blade.php         # Booking history
    │   │   └── show.blade.php          # Booking detail + cancel
    │   └── reviews/
    │       └── index.blade.php
    │
    ├── auth/                           # Auth pages (use auth layout)
    │   ├── login.blade.php
    │   ├── register.blade.php
    │   └── forgot-password.blade.php
    │
    └── emails/                         # Email templates
        ├── booking-confirmed.blade.php
        ├── booking-cancelled.blade.php
        ├── vendor-approved.blade.php
        ├── vendor-rejected.blade.php
        ├── listing-approved.blade.php
        └── payout-processed.blade.php
```

---

### How Each Layout Is Used

Each layout is extended by its own section of views:

```php
// Admin page
@extends('layouts.admin')

// Vendor page
@extends('layouts.vendor')

// Customer page
@extends('layouts.customer')

// Public page (search, listing, home)
@extends('layouts.public')

// Login / Register
@extends('layouts.auth')
```

---

### What Each Layout Contains

| Layout | Sidebar | Topbar | Footer | RTL Support |
|---|---|---|---|---|
| `public` | ❌ | ✅ (navbar) | ✅ | ✅ |
| `admin` | ✅ | ✅ | ❌ | ✅ |
| `vendor` | ✅ | ✅ | ❌ | ✅ |
| `customer` | ✅ | ✅ | ❌ | ✅ |
| `auth` | ❌ | ❌ | ❌ | ✅ |

---

### RTL Handled at Layout Level

All layouts share the same RTL logic so Arabic/Urdu users get a flipped layout automatically:

```html
<html lang="{{ app()->getLocale() }}"
      dir="{{ in_array(app()->getLocale(), ['ar', 'ur', 'he']) ? 'rtl' : 'ltr' }}">
```