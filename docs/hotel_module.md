# Hotel Module — Architecture & File Structure

## Overview

The Hotel module (`Modules/Hotel/`) is a self-contained nwidart/laravel-modules module
that handles all hotel-related functionality: content management (create/edit hotels and
rooms), multi-provider search, prebooking, checkout, and booking management.

Hotels can be created by **Admins** and **Vendors**. The core create/update logic is
centralised in shared Actions so both panels reuse exactly the same business rules.

---

## Status Flow

```
Admin creates hotel   →  status = any (admin chooses)
Vendor creates hotel  →  status = draft  (enforced by SaveHotelAction, cannot be overridden)
                               ↓
                     Admin reviews & activates
                               ↓
                          status = active  →  visible in public search
```

Only `active` hotels are returned by `LocalHotelProvider::search()` (uses `scopeActive`).

---

## Ownership

Hotels have an `author_id` (FK → `users.id`) that records who created the hotel.
Vendor panels scope all queries to `where('author_id', auth()->id())`.
Admin panels see all hotels regardless of owner.

---

## File Structure

```
Modules/Hotel/
├── app/
│   ├── Actions/
│   │   ├── SaveHotelAction.php             ← shared create/update logic (Admin + Vendor)
│   │   ├── SaveHotelRoomAction.php         ← shared room create/update logic
│   │   ├── SearchHotelAction.php           ← queries all providers, returns HotelOfferDto[]
│   │   ├── PrebookHotelAction.php          ← locks a room before payment
│   │   ├── BookHotelAction.php             ← creates Booking + BookingRoom records
│   │   ├── CheckoutHotelAction.php         ← initiates payment via PaymentService
│   │   └── GetHotelAction.php              ← fetches hotel by ID or slug
│   │
│   ├── DTOs/
│   │   ├── SearchHotelDto.php              ← city, dates, guests, filters
│   │   ├── PrebookHotelDto.php             ← room lock parameters
│   │   ├── BookHotelDto.php                ← guest info + room selection
│   │   ├── CheckoutHotelDto.php            ← payment initiation with prebook token
│   │   ├── HotelOfferDto.php               ← full hotel + rooms (search result)
│   │   ├── HotelRoomOfferDto.php           ← single room (pricing, capacity, amenities)
│   │   └── HotelOrderDto.php               ← confirmed booking details
│   │
│   ├── Enums/
│   │   ├── HotelStatusEnum.php             ← draft | active | inactive | suspended
│   │   ├── HotelProviderEnum.php           ← local | travolyo_b2b_* variants
│   │   └── BookingRoomStatusEnum.php       ← pending | confirmed | checked_in | …
│   │
│   ├── Exceptions/
│   │   └── HotelException.php
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── HotelController.php         ← public web: search, checkout, confirmation
│   │   │   └── Api/HotelController.php     ← API: search, rooms, prebook, checkout, order, cancel
│   │   │
│   │   └── Requests/
│   │       ├── Admin/
│   │       │   ├── StoreHotelRequest.php       ← admin validation (includes status/featured/sort)
│   │       │   ├── UpdateHotelRequest.php      ← extends Store; ignores current slug
│   │       │   ├── StoreHotelRoomRequest.php   ← admin room validation
│   │       │   └── UpdateHotelRoomRequest.php  ← extends Store; ignores current room slug
│   │       │
│   │       ├── Vendor/
│   │       │   ├── StoreHotelRequest.php       ← vendor validation (NO status/featured/sort)
│   │       │   ├── UpdateHotelRequest.php      ← extends Store; ignores current slug
│   │       │   ├── StoreHotelRoomRequest.php   ← vendor room validation
│   │       │   └── UpdateHotelRoomRequest.php  ← extends Store; ignores current room slug
│   │       │
│   │       ├── SearchHotelRequest.php
│   │       ├── PrebookHotelRequest.php
│   │       └── CheckoutHotelRequest.php
│   │
│   ├── Models/
│   │   ├── Hotel.php               ← author_id FK, status, amenities/services/rooms/deals relations
│   │   ├── HotelRoom.php           ← base_price, currency, bed_configuration, amenities
│   │   ├── BookingRoom.php         ← booking ↔ room pivot with check_in/out, pricing
│   │   ├── Amenity.php             ← hotel or room amenity (scopes: forHotels, forRooms)
│   │   ├── Service.php             ← hotel service with pricing model
│   │   ├── HotelDeal.php           ← special rate packages
│   │   ├── HotelDealRate.php       ← pricing tiers per date/occupancy
│   │   ├── HotelDealSupplement.php ← event-period surcharges
│   │   └── PromoCode.php           ← discount codes
│   │
│   ├── Providers/
│   │   ├── HotelProviderInterface.php              ← search / getRooms / prebook / getOrder / cancelOrder
│   │   ├── HotelServiceProvider.php
│   │   ├── RouteServiceProvider.php
│   │   ├── EventServiceProvider.php
│   │   ├── TravolyoB2BBaseHotelProvider.php        ← abstract base for all B2B providers
│   │   ├── Local/
│   │   │   ├── LocalHotelProvider.php              ← queries active hotels from DB
│   │   │   └── LocalHotelMapper.php                ← Hotel model → HotelOfferDto
│   │   ├── TravolyoB2BLocal/TravolyoB2BLocalHotelProvider.php
│   │   ├── TravolyoB2BNetStreaming/TravolyoB2BNetStreamingHotelProvider.php
│   │   └── TravolyoB2BTassPro/TravolyoB2BTassProHotelProvider.php
│   │
│   └── Resources/
│       ├── HotelOfferResource.php          ← API response for search results
│       └── HotelOrderResource.php          ← API response for booking confirmation
│
├── config/
│   └── config.php
│
├── database/
│   ├── migrations/
│   │   ├── 2026_03_13_000001_create_amenities_table.php
│   │   ├── 2026_03_13_000002_create_services_table.php
│   │   ├── 2026_03_13_000003_create_hotels_table.php          ← author_id FK
│   │   ├── 2026_03_13_000004_create_hotel_amenity_table.php
│   │   ├── 2026_03_13_000005_create_hotel_service_table.php
│   │   ├── 2026_03_13_000006_create_hotel_rooms_table.php
│   │   ├── 2026_03_13_000007_create_hotel_room_amenity_table.php
│   │   ├── 2026_03_13_000008_create_booking_rooms_table.php
│   │   ├── 2026_03_13_000009_create_hotel_deals_table.php
│   │   ├── 2026_03_13_000010_create_hotel_deal_rates_table.php
│   │   ├── 2026_03_13_000011_create_hotel_deal_supplements_table.php
│   │   ├── 2026_03_13_000012_create_promo_codes_table.php
│   │   ├── 2026_03_13_000013_create_hotel_deal_promo_code_table.php
│   │   ├── 2026_03_13_000014_add_admin_form_fields_to_hotels_table.php
│   │   ├── 2026_03_13_000015_add_media_id_columns_to_hotels_table.php
│   │   ├── 2026_03_13_000016_add_media_columns_to_hotel_rooms_table.php
│   │   └── 2026_03_13_000025_add_currency_to_hotel_rooms_table.php
│   │
│   └── seeders/
│       ├── HotelDatabaseSeeder.php
│       ├── HotelSeeder.php
│       ├── AmenitySeeder.php
│       └── ServiceSeeder.php
│
├── resources/
│   └── views/
│       ├── components/layouts/master.blade.php   ← public-facing layout
│       ├── index.blade.php                       ← hotel search listing
│       └── hotels/
│           ├── index.blade.php                   ← search results with filters
│           ├── checkout.blade.php                ← guest details + payment gateway
│           └── confirmation.blade.php            ← booking confirmation page
│
├── routes/
│   ├── web.php     ← public routes: /hotels, /hotels/checkout, /hotels/confirmation/{code}
│   └── api.php     ← API routes: /api/v1/hotels/* (search, rooms, prebook, checkout, order, cancel)
│
├── composer.json
└── module.json
```

---

## Admin Panel Integration (`Modules/Admin/`)

```
Modules/Admin/
├── app/Http/Controllers/
│   ├── HotelController.php         ← thin; delegates to SaveHotelAction + Admin FormRequests
│   └── HotelRoomController.php     ← thin; delegates to SaveHotelRoomAction + Admin FormRequests
│
├── routes/web.php                  ← Route::resource('hotels', ...) + hotel-rooms + restore
│
└── resources/views/
    ├── hotels/
    │   ├── index.blade.php         ← list all hotels (with soft-deleted), status filter
    │   ├── create.blade.php
    │   ├── edit.blade.php
    │   ├── show.blade.php
    │   └── _form.blade.php         ← includes status radio, is_featured, sort_order
    └── hotel-rooms/
        ├── index.blade.php
        ├── create.blade.php
        ├── edit.blade.php
        └── _form.blade.php
```

**Admin-only fields in the hotel form:** `status`, `is_featured`, `sort_order`.

---

## Vendor Panel Integration (`Modules/Vendor/`)

```
Modules/Vendor/
├── app/Http/Controllers/
│   ├── HotelController.php         ← thin; delegates to SaveHotelAction (isVendor:true) + Vendor FormRequests
│   │                                  scopes all queries: where('author_id', auth()->id())
│   └── HotelRoomController.php     ← thin; delegates to SaveHotelRoomAction + Vendor FormRequests
│                                      scopes all queries: whereHas('hotel', fn => where('author_id', auth()->id()))
│
├── routes/web.php                  ← Route::resource('hotels', ...) + hotel-rooms + vendor media API routes
│
└── resources/views/
    ├── media/partials/
    │   └── browser-modal.blade.php ← copy of admin's modal but uses vendor.media.* routes
    ├── hotels/
    │   ├── index.blade.php         ← vendor's own hotels only
    │   ├── create.blade.php
    │   ├── edit.blade.php
    │   ├── show.blade.php
    │   └── _form.blade.php         ← NO status/featured/sort; shows "Draft — awaiting admin review" notice
    └── hotel-rooms/
        ├── index.blade.php         ← scoped to vendor's hotel rooms
        ├── create.blade.php
        ├── edit.blade.php
        └── _form.blade.php         ← hotel dropdown filtered to vendor's own hotels
```

**Vendor restrictions (enforced server-side by SaveHotelAction with `isVendor: true`):**
- `status` is always `draft` — vendor cannot publish directly
- `is_featured` is always `false`
- `sort_order` is not changed (kept at existing value or 0 for new)

---

## Key Architectural Rules

| Rule | Where enforced |
|------|---------------|
| Vendor hotels always start as `draft` | `SaveHotelAction::buildPayload()` — `isVendor: true` overrides any submitted status |
| Vendor cannot change status, featured, sort_order | Same — fields are overwritten regardless of what the FormRequest passes |
| Vendor can only see/edit their own hotels | `HotelController` & `HotelRoomController` — `where('author_id', auth()->id())` on every query |
| Only `active` hotels appear in public search | `LocalHotelProvider::search()` — uses `Hotel::query()->active()` scope |
| Media IDs are verified against DB before saving | `SaveHotelAction::resolveMedia()` — discards any ID not in `media_files` |
| Repeater rows with all-empty fields are discarded | `SaveHotelAction::normalizeRepeater()` |

---

## Booking Flow (Public)

```
1. GET  /hotels               → search form
2. POST /api/v1/hotels/search → SearchHotelAction  → HotelOfferDto[]
3. POST /api/v1/hotels/rooms  → provider->getRooms()
4. POST /api/v1/hotels/prebook → PrebookHotelAction → cache checkout data (30 min)
5. GET  /hotels/checkout?token=... → load cached prebook data
6. POST /api/v1/hotels/checkout   → CheckoutHotelAction → PaymentService
7. GET  /hotels/confirmation/{code} → booking confirmation
```

---

## Run after schema changes

```bash
php artisan migrate:fresh --seed
```

The `author_id` column was renamed from `user_id` in migration
`2026_03_13_000003_create_hotels_table.php`.
