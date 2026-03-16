<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'label',
        'symbol',
        'flag',
        'name',
        'is_active',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public static function supported(): array
    {
        if (!Schema::hasTable('currencies')) {
            return (array) config('currency.supported', []);
        }

        $items = static::query()
            ->active()
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get();

        if ($items->isEmpty()) {
            return (array) config('currency.supported', []);
        }

        return $items->mapWithKeys(function (self $currency) {
            return [
                strtoupper($currency->code) => [
                    'label' => $currency->label ?: strtoupper($currency->code),
                    'symbol' => $currency->symbol ?: strtoupper($currency->code),
                    'flag' => strtolower($currency->flag ?: 'us'),
                    'name' => $currency->name ?: strtoupper($currency->code),
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
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->value('code');

        return strtoupper((string) ($default ?: config('currency.default', 'USD')));
    }
}
