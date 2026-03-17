# Activity Module — Architecture & File Structure

## Overview

The Activity module (`Modules/Activity/`) is a self-contained nwidart/laravel-modules module
that handles all activity-related functionality: public listing, checkout, booking management,
and content management for both Admin and Vendor panels.

Activities can be created by **Admins** and **Vendors**. The core create/update logic is
centralised in `SaveActivityAction` so both panels share exactly the same business rules.

---

## Status Flow

```
Admin creates activity  →  status = any (admin chooses: publish | draft | pending)
Vendor creates activity →  status = pending  (enforced by SaveActivityAction, cannot be overridden)
                                   ↓
                        Admin reviews & approves
                                   ↓
                           status = publish  →  visible in public listing
```

Only `publish` + `is_active = true` activities are returned by the public listing
(uses `scopePublished` on the Activity model).

---

## Ownership

Activities have an `author_id` (FK → `users.id`) that records who created the activity.
Vendor panels scope all queries to `where('author_id', auth()->id())`.
Admin panels see all activities regardless of owner.

---

## File Structure

```
Modules/Activity/
├── app/
│   ├── Actions/
│   │   └── SaveActivityAction.php          ← shared create/update logic (Admin + Vendor)
│   │                                          resolves media, normalises extra_information,
│   │                                          enforces vendor restrictions
│   │
│   ├── Enums/
│   │   └── ActivityStatusEnum.php          ← publish | draft | pending
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── ActivityController.php      ← public web: listing, show, checkout, pay, booking detail
│   │   │   ├── Admin/
│   │   │   │   └── ActivityController.php  ← base admin CRUD (extended by Admin module thin wrapper)
│   │   │   └── Vendor/
│   │   │       └── ActivityController.php  ← base vendor CRUD (extended by Vendor module thin wrapper)
│   │   │                                      scopes all queries: where('author_id', auth()->id())
│   │   │
│   │   └── Requests/
│   │       ├── Admin/
│   │       │   ├── StoreActivityRequest.php    ← admin validation (includes status, author_id, is_active)
│   │       │   └── UpdateActivityRequest.php   ← extends Store; ignores current slug
│   │       └── Vendor/
│   │           ├── StoreActivityRequest.php    ← vendor validation (NO status/author_id/is_active)
│   │           └── UpdateActivityRequest.php   ← extends Store; ignores current slug
│   │
│   ├── Models/
│   │   ├── Activity.php                    ← author_id FK, status, is_active, gallery, scopePublished
│   │   └── ActivityBookingPassenger.php    ← per-booking passenger details
│   │
│   └── Providers/
│       ├── ActivityServiceProvider.php
│       ├── RouteServiceProvider.php
│       └── EventServiceProvider.php
│
├── database/
│   └── migrations/
│       ├── 2026_03_16_000006_create_activities_table.php          ← in root database/migrations/
│       └── 2026_03_16_000007_create_activity_booking_passengers_table.php
│
├── resources/
│   └── views/
│       ├── index.blade.php                 ← public activity listing with filters
│       ├── show.blade.php                  ← single activity detail page
│       ├── checkout.blade.php              ← passenger details + payment gateway
│       ├── booking-detail.blade.php        ← booking confirmation page
│       └── partials/
│           ├── _activity-card.blade.php
│           └── _filters.blade.php
│
├── routes/
│   ├── web.php     ← public routes: /activities, /activities/{slug}, /activities/{id}/checkout
│   └── api.php     ← (empty — no API endpoints yet)
│
├── composer.json
└── module.json
```

---

## Admin Panel Integration (`Modules/Admin/`)

```
Modules/Admin/
├── app/Http/Controllers/
│   └── ActivityController.php      ← thin wrapper; extends Activity::Admin\ActivityController
│
├── routes/web.php                  ← Route::resource('activities', ...) (except show)
│
└── resources/views/
    └── activities/
        ├── index.blade.php         ← list all activities, status filter (shows pending from vendors)
        ├── create.blade.php
        ├── edit.blade.php
        └── _form.blade.php         ← includes status dropdown (publish/draft/pending), author, is_active
```

**Admin-only fields in the activity form:** `status`, `author_id`, `is_active`.

---

## Vendor Panel Integration (`Modules/Vendor/`)

```
Modules/Vendor/
├── app/Http/Controllers/
│   └── ActivityController.php      ← thin wrapper; extends Activity::Vendor\ActivityController
│                                      scopes all queries: where('author_id', auth()->id())
│
├── routes/web.php                  ← Route::resource('activities', ...) (except show)
│
└── resources/views/
    └── activities/
        ├── index.blade.php         ← vendor's own activities only (status badge shows pending/publish)
        ├── create.blade.php
        ├── edit.blade.php
        └── _form.blade.php         ← NO status/author/is_active; shows "Pending review" notice
```

**Vendor restrictions (enforced server-side by `SaveActivityAction` with `isVendor: true`):**
- `status` is always `pending` — vendor cannot publish directly
- `is_active` is always `false` — admin controls visibility
- After any vendor edit, status reverts to `pending` — admin must re-approve

---

## Key Architectural Rules

| Rule | Where enforced |
|------|---------------|
| Vendor activities always start as `pending` | `SaveActivityAction::buildPayload()` — `isVendor: true` overwrites any submitted status |
| Vendor cannot change `status` or `is_active` | Same — fields are overwritten regardless of what the FormRequest passes |
| Vendor edit resets status to `pending` | Same — status forced on every update when `isVendor: true` |
| Vendor can only see/edit their own activities | `Vendor\ActivityController` — `where('author_id', auth()->id())` + `abort_unless(403)` on write |
| Only `publish` + `is_active` activities appear publicly | `Activity::scopePublished()` — `where status=publish AND is_active=true` |
| Media IDs are verified against DB before saving | `SaveActivityAction::resolveMedia()` — discards any ID not in `media_files` |
| `extra_information` blank items are discarded | `SaveActivityAction::normalizeExtraInformation()` |

---

## Approval Workflow

```
1. Vendor submits activity        → status = pending, is_active = false
2. Admin sees it in index list    → filter by status=pending
3. Admin opens edit form          → changes status to publish + is_active = true
4. Activity goes live             → visible in public /activities listing
```

> Tip: Add a quick **Approve** button to the admin index row (POST status=publish + is_active=1)
> so admin doesn't need to open the full edit form for simple approvals.

---

## Public Booking Flow

```
1. GET  /activities                     → listing (scopePublished)
2. GET  /activities/{slug}              → single activity detail
3. GET  /activities/{id}/checkout       → passenger form, draft booking
4. POST /activities/{id}/checkout/pay   → create/update booking, initiate payment
5. GET  /activities/booking/{code}      → booking confirmation
```

---

## Run after schema changes

```bash
php artisan migrate:fresh --seed
```

The `activities` table migration lives in the root `database/migrations/` folder
(`2026_03_16_000006_create_activities_table.php`), not inside the module.