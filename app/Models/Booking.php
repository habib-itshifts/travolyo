<?php

namespace App\Models;

use App\Enums\BookingObjectModelEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Booking extends Model
{
    use SoftDeletes;

    const DRAFT          = 'draft';
    const UNPAID         = 'unpaid';
    const CONFIRMED      = 'confirmed';
    const COMPLETED      = 'completed';
    const PAID           = 'paid';
    const CANCELLED      = 'cancelled';
    const BOOKING_FAILED = 'booking_failed';

    const COMMISSION_PERCENT = 'percent';
    const COMMISSION_FIXED   = 'fixed';

    const REFUND_NONE    = 'none';
    const REFUND_PENDING = 'pending';
    const REFUND_PARTIAL = 'partial';
    const REFUND_FULL    = 'full';

    protected $table = 'bookings';

    protected $fillable = [
        'code', 'object_model', 'object_id',
        'author_id', 'customer_id', 'vendor_id',
        'status', 'is_paid',
        'start_date', 'end_date', 'total_guests',
        'currency',
        'total_before_discount', 'coupon_amount', 'total_before_fees',
        'buyer_fees', 'total', 'pay_now', 'paid',
        'commission_type', 'commission', 'commission_amount',
        'vendor_service_fee', 'vendor_amount', 'vendor_payout_id', 'vendor_paid_at',
        'refund_amount', 'refund_status', 'refunded_at',
        'first_name', 'last_name', 'email', 'phone',
        'address', 'city', 'state', 'zip_code', 'country',
        'customer_notes',
        'source', 'platform',
        'create_user', 'update_user',
    ];

    protected $casts = [
        'total'                => 'float',
        'pay_now'              => 'float',
        'paid'                 => 'float',
        'total_before_discount'=> 'float',
        'coupon_amount'        => 'float',
        'total_before_fees'    => 'float',
        'buyer_fees'           => 'float',
        'commission'           => 'float',
        'commission_amount'    => 'float',
        'vendor_service_fee'   => 'float',
        'vendor_amount'        => 'float',
        'refund_amount'        => 'float',
        'is_paid'              => 'boolean',
        'start_date'           => 'datetime',
        'end_date'             => 'datetime',
        'vendor_paid_at'       => 'datetime',
        'refunded_at'          => 'datetime',
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

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
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

    /**
     * Recompute commission_amount and vendor_amount from current total.
     * Call before saving whenever pricing changes.
     */
    public function applyCommission(): void
    {
        $total = (float) $this->total;

        if ($this->commission_type === self::COMMISSION_PERCENT) {
            $this->commission_amount = round($total * ((float) $this->commission / 100), 2);
        } elseif ($this->commission_type === self::COMMISSION_FIXED) {
            $this->commission_amount = (float) $this->commission;
        }

        $this->vendor_amount = max(0, round(
            $total - $this->commission_amount - (float) $this->vendor_service_fee,
            2
        ));
    }

    /** Amount still outstanding from the customer. */
    public function getBalanceDueAttribute(): float
    {
        return max(0, round($this->total - $this->paid, 2));
    }

    /** Platform's net earnings on this booking (commission + vendor fee + buyer fees). */
    public function getPlatformEarningsAttribute(): float
    {
        return round($this->commission_amount + $this->vendor_service_fee + $this->buyer_fees, 2);
    }

    public function markAsPaid(): void
    {
        $this->is_paid = true;
        $this->status  = self::COMPLETED;
        $this->paid    = $this->pay_now;
        $this->applyCommission();
        $this->save();
    }

    public function markAsPaymentFailed(): void
    {
        $this->status = self::BOOKING_FAILED;
        $this->save();
    }

    /**
     * Detect platform (web|mobile) from X-Platform header, falling back to User-Agent.
     */
    public static function detectPlatform(): string
    {
        $header = request()->header('X-Platform');
        if ($header && in_array(strtolower($header), ['mobile', 'web'])) {
            return strtolower($header);
        }

        $ua = strtolower(request()->userAgent() ?? '');

        return (str_contains($ua, 'mobile') || str_contains($ua, 'android') || str_contains($ua, 'iphone'))
            ? 'mobile'
            : 'web';
    }

    public function getDetailUrl(): string
    {
        if ($this->object_model === BookingObjectModelEnum::Activity->value) {
            return route('activities.booking.detail', ['code' => $this->code]);
        }

        return route('bookings.show', ['code' => $this->code]);
    }
}
