# Travolyo

A multi-role travel platform built with Laravel 11. Supports three panels — **Admin**, **Vendor**, and **Customer** — each as a self-contained nwidart module.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 11.31 |
| PHP | ^8.2 |
| Auth | Laravel Breeze |
| Roles & Permissions | spatie/laravel-permission |
| Media | spatie/laravel-medialibrary |
| Activity Log | spatie/laravel-activitylog |
| Modules | nwidart/laravel-modules v12 |
| Frontend | Vite + Tailwind CSS + Alpine.js |
| Payments | Stripe |
| Social Auth | Laravel Socialite |
| Queue | Laravel Horizon (database driver) |
| API Docs | Scribe |

---

## Modules

| Module | URL Prefix | Route Prefix | Middleware |
|---|---|---|---|
| Admin | `/admin` | `admin.*` | `admin` |
| Vendor | `/vendor` | `vendor.*` | `vendor` |
| Customer | `/customer` | `customer.*` | `customer` |

---

## Project Setup

### Requirements

- PHP >= 8.2
- Composer
- Node.js >= 18 + npm
- MySQL (or any supported DB)
- Redis (optional, for queues/cache)

---

### 1. Clone the repository

```bash
git clone <repository-url> travolyo
cd travolyo
```

---

### 2. Install PHP dependencies

```bash
composer install
```

---

### 3. Install Node dependencies

```bash
npm install
```

---

### 4. Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

Then open `.env` and configure:

```env
APP_NAME=Travolyo
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=travolyo
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=log

QUEUE_CONNECTION=database
CACHE_STORE=database
SESSION_DRIVER=database
```

---

### 5. Run migrations and seeders

> **Note:** This project uses `migrate:fresh` workflow — all schema changes are merged into original migration files.

```bash
php artisan migrate:fresh --seed
```

This will run all seeders in order:

1. `RoleSeeder` — creates `admin`, `vendor`, `customer` roles
2. `PermissionSeeder` — attaches permissions to roles
3. `UserSeeder` — seeds default admin, vendor, and customer users
4. `HotelDatabaseSeeder` — seeds sample hotels and rooms

---

### 6. Storage setup

```bash
php artisan storage:link
```

---

### 7. Build frontend assets

For development (with hot reload):

```bash
npm run dev
```

For production:

```bash
npm run build
```

---

### 8. Start the development server

```bash
php artisan serve
```

App will be available at: `http://127.0.0.1:8000`

---

## Default Seeded Users

| Role | Email | Password |
|---|---|---|
| Admin | `admin@travolyo.com` | `password` |
| Vendor | `vendor@travolyo.com` | `password` |
| Customer | `customer@travolyo.com` | `password` |

---

## Vendor Onboarding Flow

1. Customer submits a "Become a Vendor" request → `vendor_status = pending`
2. Admin approves the request → `vendor_status = approved`, `vendor` role assigned
3. Vendor uploads verification documents (National ID, Business Registration, etc.)
4. Vendor submits documents for review → `vendor_status = docs_submitted`
5. Admin reviews each document individually (approve / reject with note)
6. Admin marks vendor as verified → `vendor_status = verified`

---

## Useful Artisan Commands

```bash
# Run migrations fresh with seeders
php artisan migrate:fresh --seed

# Clear all caches
php artisan optimize:clear

# Generate IDE helper files (if installed)
php artisan ide-helper:generate

# List all registered routes
php artisan route:list

# Run queue worker
php artisan queue:work

# Start Laravel Horizon (queue dashboard)
php artisan horizon

# Generate API documentation (Scribe)
php artisan scribe:generate

# Create a new module
php artisan module:make ModuleName

# List all modules
php artisan module:list
```

---

## Module Structure

Each module follows the same self-contained structure:

```
Modules/Admin/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   └── Providers/
│       └── AdminServiceProvider.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
│       ├── components/
│       │   └── layouts/
│       │       ├── master.blade.php
│       │       └── partials/
│       │           ├── sidebar.blade.php
│       │           └── topbar.blade.php
│       └── dashboard.blade.php
└── routes/
    └── web.php
```

---

## License

Private project — all rights reserved.