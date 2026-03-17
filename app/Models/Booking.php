<?php

namespace App\Models;

use App\Enums\BookingObjectModelEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Booking extends Model
{
    const DRAFT = 'draft';
    const UNPAID = 'unpaid';
    const CONFIRMED = 'confirmed';
    const COMPLETED = 'completed';
    const PAID = 'paid';
    const CANCELLED = 'cancelled';
    const BOOKING_FAILED = 'booking_failed';

    protected $table = 'bookings';

    protected $fillable = [
        'code', 'object_model', 'customer_id',
        'status', 'total', 'pay_now', 'paid', 'currency',
        'first_name', 'last_name', 'email', 'phone', 'customer_notes',
    ];

    protected $casts = [
        'total' => 'float',
        'pay_now' => 'float',
        'paid' => 'float',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $booking) {
            if (empty($booking->code)) {
                $booking->code = 'TRV-' . date('Y') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function metaItems(): HasMany
    {
        return $this->hasMany(BookingMeta::class);
    }

    public function getMeta(string $name, mixed $default = null): mixed
    {
        $item = $this->metaItems()->where('name', $name)->first();

        return $item ? $item->val : $default;
    }

    public function getJsonMeta(string $name): array
    {
        $val = $this->getMeta($name);
        if (empty($val)) {
            return [];
        }

        return is_array($val) ? $val : (json_decode($val, true) ?: []);
    }

    public function addMeta(string $name, mixed $val): void
    {
        $this->metaItems()->create([
            'name' => $name,
            'val' => is_array($val) ? json_encode($val) : (string) $val,
        ]);
    }

    public function updateMeta(string $name, mixed $val): void
    {
        $encoded = is_array($val) ? json_encode($val) : (string) $val;

        $this->metaItems()->updateOrCreate(
            ['name' => $name],
            ['val' => $encoded]
        );
    }

    public function markAsPaid(): void
    {
        $this->status = self::COMPLETED;
        $this->paid = $this->pay_now;
        $this->save();
    }

    public function markAsPaymentFailed(): void
    {
        $this->status = self::BOOKING_FAILED;
        $this->save();
    }

    public function getDetailUrl(): string
    {
        if ($this->object_model === BookingObjectModelEnum::Activity->value) {
            return route('activities.booking.detail', ['code' => $this->code]);
        }

        return route('bookings.show', ['code' => $this->code]);
    }
}
