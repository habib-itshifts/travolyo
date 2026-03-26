<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Currency extends Model
{
    protected $fillable = [
        'name',
        'code',
        'symbol',
        'format',
        'exchange_rate',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'exchange_rate' => 'decimal:8',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    /**
     * Map currency code to country flag code.
     */
    private static function flagFromCode(string $code): string
    {
        $map = [
            'USD' => 'us', 'GBP' => 'gb', 'EUR' => 'eu', 'AED' => 'ae',
            'SAR' => 'sa', 'TRY' => 'tr', 'PKR' => 'pk', 'INR' => 'in',
            'CAD' => 'ca', 'AUD' => 'au', 'JPY' => 'jp', 'CNY' => 'cn',
            'CHF' => 'ch', 'SGD' => 'sg', 'MYR' => 'my', 'THB' => 'th',
            'QAR' => 'qa', 'KWD' => 'kw', 'BHD' => 'bh', 'OMR' => 'om',
            'EGP' => 'eg', 'JOD' => 'jo', 'MAD' => 'ma', 'NZD' => 'nz',
        ];

        return $map[strtoupper($code)] ?? strtolower(substr($code, 0, 2));
    }

    public static function supported(): array
    {
        if (!Schema::hasTable('currencies')) {
            return (array) config('currency.supported', []);
        }

        $items = static::query()
            ->active()
            ->orderBy('code')
            ->get();

        if ($items->isEmpty()) {
            return (array) config('currency.supported', []);
        }

        return $items->mapWithKeys(function (self $currency) {
            return [
                strtoupper($currency->code) => [
                    'label'  => strtoupper($currency->code),
                    'symbol' => $currency->symbol ?: strtoupper($currency->code),
                    'flag'   => self::flagFromCode($currency->code),
                    'name'   => $currency->name ?: strtoupper($currency->code),
                ],
            ];
        })->all();
    }

    public static function defaultCode(): string
    {
        if (!Schema::hasTable('currencies')) {
            return strtoupper((string) config('currency.default', 'USD'));
        }

        $default = static::query()
            ->active()
            ->orderBy('code')
            ->value('code');

        return strtoupper((string) ($default ?: config('currency.default', 'USD')));
    }
}
