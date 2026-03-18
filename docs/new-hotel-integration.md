# Hotel Module — Integration Guide

> **Last updated:** 2026-03-18 (prebook + booking added)
> **Purpose:** Reference for anyone adding a new hotel provider so you don't need to re-study the module from scratch.

---

## 1. Module Overview

The Hotel module lives at `Modules/Hotel/`. It follows a **provider pattern**: every supplier (local DB, B2B aggregator, Hyperguest, …) implements the same `HotelProviderInterface` and the rest of the stack (actions, controller, resources) is completely provider-agnostic.

### End-to-end request flow

```
GET  /hotels                       → HotelController::index()         (search form / listing page)
POST /api/hotels/search            → Api/HotelController::search()    → SearchHotelAction → Provider::search()
POST /api/hotels/rooms             → Api/HotelController::rooms()     → Provider::getRooms()
POST /api/hotels/prebook           → Api/HotelController::prebook()   → PrebookHotelAction → Provider::prebook()
GET  /hotels/checkout?token=...    → HotelController::checkout()      (guest-details form)
POST /api/hotels/checkout          → Api/HotelController::checkout()  → CheckoutHotelAction → PaymentService
GET  /hotels/confirmation/{code}   → HotelController::confirmation()  (booking summary)
```

---

## 2. Provider Interface

`Modules/Hotel/app/Providers/HotelProviderInterface.php`

```php
interface HotelProviderInterface
{
    public function search(SearchHotelDto $dto): array;            // HotelOfferDto[]
    public function getRooms(string $offerId, string $cityCode,
                             string $checkIn, string $checkOut,
                             int $adults, int $children): array;   // HotelRoomOfferDto[]
    public function prebook(PrebookHotelDto $dto): HotelOfferDto;
    public function getOrder(string $orderId): HotelOrderDto;
    public function cancelOrder(string $orderId): bool;
}
```

---

## 3. Existing Providers

| Enum value | Class | Source |
|---|---|---|
| `local` | `Local/LocalHotelProvider` | MySQL DB (admin/vendor hotels) |
| `travolyo_b2b_local` | `TravolyoB2BLocal/TravolyoB2BLocalHotelProvider` | TravolyoB2B REST API (source=local) |
| `travolyo_b2b_net_streaming` | `TravolyoB2BNetStreaming/TravolyoB2BNetStreamingHotelProvider` | TravolyoB2B REST API (source=netstorming_api) |
| `travolyo_b2b_tasspro` | `TravolyoB2BTassPro/TravolyoB2BTassProHotelProvider` | TravolyoB2B REST API (source=tasspro_api) |
| `hyperguest` | `Hyperguest/HyperguestHotelProvider` | JSON file (mock) → live API later |

---

## 4. Hyperguest Integration (added 2026-03-18)

### 4.1 Status
**Mock / JSON-based.** No live API credentials yet. All data is read from:

```
public/data/hyperguest/hotels.json
```

When credentials arrive, replace `HyperguestHotelProvider::loadData()` with an HTTP call — the mapper and the rest of the stack stay untouched.

### 4.2 Files created

| File | Purpose |
|---|---|
| `public/data/hyperguest/hotels.json` | Sample hotel + room data (4 Dubai hotels, 11 rooms) |
| `Modules/Hotel/app/Providers/Hyperguest/HyperguestHotelMapper.php` | Maps raw JSON arrays → `HotelOfferDto` / `HotelRoomOfferDto` |
| `Modules/Hotel/app/Providers/Hyperguest/HyperguestHotelProvider.php` | Implements `HotelProviderInterface` against JSON file |

### 4.3 Files modified

| File | Change |
|---|---|
| `Modules/Hotel/app/Enums/HotelProviderEnum.php` | Added `case Hyperguest = 'hyperguest'` |
| `Modules/Hotel/app/Actions/SearchHotelAction.php` | Added `Hyperguest => new HyperguestHotelProvider()` to match |
| `Modules/Hotel/app/Http/Controllers/Api/HotelController.php` | Added Hyperguest to `rooms()` match + import |
| `Modules/Hotel/app/Http/Requests/SearchHotelRequest.php` | Added `hyperguest` to `provider` in-list |
| `Modules/Hotel/app/Http/Requests/PrebookHotelRequest.php` | Added `hyperguest` to `provider` in-list |

### 4.4 JSON data structure

```json
{
  "status": "success",
  "hotels": [
    {
      "hotel_id": "HG001",
      "name": "...",
      "star_rating": 5,
      "city": "Dubai",
      "country": "UAE",
      "address": "...",
      "latitude": 25.2048,
      "longitude": 55.2708,
      "description": "...",
      "short_description": "...",
      "check_in_time": "14:00",
      "check_out_time": "12:00",
      "images": ["url1", "url2"],
      "amenities": ["Free WiFi", "Pool", "..."],
      "rooms": [
        {
          "room_id": "HG001-R001",
          "name": "Deluxe King Room",
          "room_type": "standard",
          "bed_configuration": {"king": 1},
          "max_adults": 2,
          "max_children": 1,
          "size_sqm": 35,
          "view_type": "City View",
          "description": "...",
          "amenities": ["AC", "TV", "..."],
          "images": ["url"],
          "rates": {
            "base_price_per_night": 250.00,
            "currency": "USD",
            "is_available": true,
            "cancellation_policy": "...",
            "meal_plan": "Room Only"
          }
        }
      ]
    }
  ]
}
```

### 4.5 Booking request structure (Hyperguest API)

`POST https://[book endpoint domain]/2.0/booking/create`

```json
{
  "dates":        { "from": "YYYY-MM-DD", "to": "YYYY-MM-DD" },
  "propertyId":   10001,
  "leadGuest": {
    "birthDate": "1990-01-01",
    "title": "MR",
    "name":    { "first": "...", "last": "..." },
    "contact": { "address": "...", "city": "...", "country": "...",
                 "email": "...", "phone": "...", "state": "...", "zip": "..." }
  },
  "reference": { "agency": "travolyo-<uid>" },
  "rooms": [{
    "roomCode":      "DBL",
    "rateCode":      "BAR",
    "expectedPrice": { "amount": 343.20, "currency": "USD" },
    "guests":        [{ "birthDate": "...", "name": {...}, "title": "MR" }],
    "specialRequests": ["..."]
  }],
  "meta":         [{ "key": "Source", "value": "Travolyo" }],
  "isTest":       true,
  "groupBooking": false
}
```

### 4.6 Booking response structure

Key fields extracted and stored in `Booking` meta:

| Meta key | Source in response | Purpose |
|---|---|---|
| `hyperguest_booking_id` | `bookingId` | Supplier booking reference |
| `hyperguest_status` | `content.status` | "Confirmed" / "Pending" / "Failed" |
| `hyperguest_cancellation_policy` | `rooms[0].cancellationPolicy` | Array of penalty windows |
| `hyperguest_remarks` | `rooms[0].remarks` | Important messages to show the guest |
| `hyperguest_booking` | full response | Complete raw response for auditing |

Key price fields in response:
- `content.prices.sell.price` — total sell price (overrides our cached price)
- `content.prices.net.price`  — net price (cost to us)
- `content.prices.commission.price` — our commission

### 4.7 Room ID encoding

Each `HotelRoomOfferDto::$roomId` is a **base64-encoded JSON string** containing:

```json
{
  "hotel_id":    "HG001",
  "property_id": 10001,
  "room_id":     "HG001-R001",
  "room_code":   "DBL",
  "rate_code":   "BAR",
  "price":       250.00,
  "currency":    "USD",
  "meal_plan":   "Room Only"
}
```

These keys flow through `search → prebook → checkout → book()` as an opaque string.
Decode with `HyperguestHotelMapper::decodeBookingKey($roomId)`.

### 4.8 How search works (mock)

1. `SearchHotelAction` includes `HotelProviderEnum::Hyperguest` in its provider loop.
2. `HyperguestHotelProvider::search()` calls `loadData()` → reads JSON file.
3. Hotels are filtered by **case-insensitive city match** and optional `star_rating`.
4. Each hotel is mapped via `HyperguestHotelMapper::toOfferDto()`.
5. Price filters (`price_min` / `price_max`) are applied against `lowestPrice`.
6. Returns `HotelOfferDto[]` — same shape as every other provider.

### 4.9 Switching to the live API

When the Hyperguest API credentials are ready:

1. Replace `HyperguestHotelProvider::loadData()`:
   ```php
   private function loadData(): array
   {
       $response = Http::withToken(config('hyperguest.api_key'))
           ->post(config('hyperguest.base_url') . '/hotels/search', [
               'destination' => $this->lastSearchCity,
               // ... other params
           ]);

       return $response->json('hotels', []);
   }
   ```
2. Implement `getOrder()` and `cancelOrder()` against the Hyperguest booking API.
3. Add `HYPERGUEST_API_KEY` and `HYPERGUEST_BASE_URL` to `.env`.
4. Add a `config/hyperguest.php` config file (or extend `Modules/Hotel/config/config.php`).

---

## 5. How to Add a Brand-New Provider

1. **Add enum case** in `Modules/Hotel/app/Enums/HotelProviderEnum.php`:
   ```php
   case MyProvider = 'my_provider';
   ```

2. **Create a directory**: `Modules/Hotel/app/Providers/MyProvider/`

3. **Create a Mapper** `MyProviderHotelMapper.php`:
   - `toOfferDto(mixed $rawHotel, int $nights, string $currency): HotelOfferDto`
   - `toRoomOfferDto(mixed $rawRoom, int $nights, string $currency): HotelRoomOfferDto`

4. **Create a Provider** `MyProviderHotelProvider.php` implementing `HotelProviderInterface`:
   - `search()` → fetch data, filter, map via mapper, return `HotelOfferDto[]`
   - `getRooms()` → return `HotelRoomOfferDto[]` (or `[]` if rooms are in search result)
   - `prebook()` → lock rate, return updated `HotelOfferDto`
   - `getOrder()` / `cancelOrder()` → order management

5. **Register in SearchHotelAction** (`resolveProvider` match):
   ```php
   HotelProviderEnum::MyProvider => new MyProviderHotelProvider(),
   ```

6. **Register in Api/HotelController** (`rooms()` match):
   ```php
   HotelProviderEnum::MyProvider => new MyProviderHotelProvider(),
   ```

7. **Add to validation** in `SearchHotelRequest` and `PrebookHotelRequest`:
   ```php
   'provider' => ['nullable', 'string', 'in:local,...,my_provider'],
   ```

---

## 6. Key DTOs

| DTO | When used |
|---|---|
| `SearchHotelDto` | Input to every `search()` call |
| `HotelOfferDto` | Output of `search()` and `prebook()` |
| `HotelRoomOfferDto` | Individual room inside `HotelOfferDto::$rooms` and `getRooms()` |
| `PrebookHotelDto` | Input to `prebook()` |
| `HotelOrderDto` | Output of `getOrder()` |
| `CheckoutHotelDto` | Input to `CheckoutHotelAction` (guest details + payment) |

---

## 7. Important Notes

- **Rooms in search vs. separate call:** `LocalHotelProvider` and `HyperguestHotelProvider` embed rooms inside the search result. The B2B providers return `rooms=[]` from `search()` and require a separate `POST /api/hotels/rooms` call.
- **Room IDs are opaque:** The front-end passes `room_id` back as-is to `/prebook`. B2B providers encode booking keys as base64 JSON in the room ID.
- **Checkout cache:** After prebook, checkout data is cached for 30 minutes under a UUID token. The token is passed as `?token=` to `/hotels/checkout`.
- **Provider isolation:** Failed providers are silently skipped in `SearchHotelAction` so one broken provider never blocks others.
