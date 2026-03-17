<?php

namespace Modules\Vendor\Models;

use App\Enums\VendorDocumentStatusEnum;
use App\Enums\VendorDocumentTypeEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorDocument extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'status',
        'admin_note',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'type'        => VendorDocumentTypeEnum::class,
            'status'      => VendorDocumentStatusEnum::class,
            'reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === VendorDocumentStatusEnum::Pending;
    }

    public function isApproved(): bool
    {
        return $this->status === VendorDocumentStatusEnum::Approved;
    }

    public function isRejected(): bool
    {
        return $this->status === VendorDocumentStatusEnum::Rejected;
    }
}
