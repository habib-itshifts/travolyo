# Currency System (Torann/laravel-currency)

## Package
- **Package:** `torann/currency`
- **Config:** `config/currency.php`
- **Driver:** `database` (reads from `currencies` table)
- **Default currency:** `USD` (set in config)

## Database Table (`currencies`)

| Column        | Type       | Example          |
|---------------|------------|------------------|
| name          | string     | US Dollar        |
| code          | string(10) | USD              |
| symbol        | string(25) | $                |
| format        | string(50) | $1,0.00          |
| exchange_rate | string     | 1.00000000       |
| active        | boolean    | true             |

## Format Syntax

Torann uses a format string to decide how to display a currency value. The format is built from these parts:

```
[symbol] 1 [thousands_sep] 0 [decimal_sep] 00 [symbol]
```

- `1` = the integer part of the number
- `0` = decimal digits (each `0` = one decimal place, so `00` = 2 decimals)
- Character between `1` and first `0` = **thousands separator**
- Character between the `0`s = **decimal separator**
- Symbol goes before or after the number (wherever you place it)

### Examples

| Currency | Format       | Input   | Output         |
|----------|-------------|---------|----------------|
| USD      | `$1,0.00`   | 1500.50 | $1,500.50      |
| GBP      | `£1,0.00`   | 1500.50 | £1,500.50      |
| EUR      | `1.0,00€`   | 1500.50 | 1.500,50€      |
| AED      | `1,0.00 د.إ` | 1500.50 | 1,500.50 د.إ   |
| JPY      | `¥1,0.`     | 1500    | ¥1,500         |
| INR      | `₹1,0.00`   | 1500.50 | ₹1,500.50      |
| PKR      | `Rs 1,0.00` | 1500.50 | Rs 1,500.50    |

### Rules
- If the symbol contains dots (like `د.إ`), put it **after** the number to avoid confusing the parser
- EUR uses `.` as thousands and `,` as decimal (European style)
- For zero decimal currencies (JPY), use `¥1,0.` with nothing after the decimal

## The `currency()` Helper

| Call                                | What it does                                                  |
|-------------------------------------|---------------------------------------------------------------|
| `currency()`                        | Returns the Currency manager object                           |
| `currency(100)`                     | Converts 100 from default (USD) to user's selected currency   |
| `currency(100, 'USD', 'GBP')`      | Converts 100 from USD to GBP explicitly                       |
| `currency()->getUserCurrency()`     | Returns user's selected currency code (from session)          |
| `currency()->getCurrency()`         | Returns details of the current currency                       |
| `currency()->getCurrencies()`       | Returns all active currencies                                 |

## How User's Currency is Set

1. Customer clicks currency in navbar dropdown
2. Hits `GET /currency/{code}` route
3. Saves `currency` to session
4. `CurrencyMiddleware` (in `web` middleware group) reads session on every request
5. `currency()` helper uses that currency for conversions

## Usage in Blade Views

```blade
{{-- Convert price from original currency to user's selected currency --}}
<span class="price">{{ currency($price, $originalCurrency) }}</span>

{{-- Example: hotel priced in USD, customer browsing in GBP --}}
<span>{{ currency($hotel->price_per_night, 'USD') }}</span>

{{-- Show both converted + original at checkout --}}
<span class="price">{{ currency($price, $originalCurrency) }}</span>
<small class="text-muted">Actual charge: {{ $originalCurrency }} {{ number_format($price, 2) }}</small>
```

## Usage in PHP (Controllers / Actions)

```php
// Convert 500 USD to user's selected currency
$converted = currency(500, 'USD');

// Convert between specific currencies
$gbpPrice = currency(500, 'USD', 'GBP');

// Get user's current currency
$userCurrency = currency()->getUserCurrency(); // e.g. "GBP"

// Get all active currencies
$all = currency()->getCurrencies();
```

## Where to Use in the System

| Page                 | What to do                                          |
|----------------------|-----------------------------------------------------|
| Hotel search results | `currency($hotel->price_per_night, $hotel->currency)` |
| Flight search results| `currency($offer->total_amount, $offer->currency)`    |
| Hotel deal cards     | `currency($deal->price, $deal->currency)`              |
| Booking summary      | Show converted + original price                        |
| Checkout             | Show original provider price only                      |

## Exchange Rate Updates

```bash
# Manual update (requires API key)
php artisan currency:update

# Schedule daily (in console/routes or Kernel)
Schedule::command('currency:update')->daily();
```

- API key from openexchangerates.org (free tier: 1,000 req/month)
- Add to `.env`: `CURRENCY_API_KEY=your_key_here`
- Rates are stored in DB, served from cache - no API call per request

## Important Notes

- Displayed prices are **approximate** (based on daily rates)
- At checkout, always show the **original provider currency + price**
- The conversion is for **display only** - actual charge happens in provider's currency
- This is how Skyscanner, Google Flights, Booking.com all work
