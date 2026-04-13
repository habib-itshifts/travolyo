<?php

namespace Modules\Space\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpaceAvailability extends Model
{
    protected $fillable = [
        'space_id',
        'date',
        'is_available',
        'price_override',
        'min_stay',
        'notes',
    ];

    protected $casts = [
        'date'           => 'date',
        'is_available'   => 'boolean',
        'price_override' => 'decimal:2',
        'min_stay'       => 'integer',
    ];

    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
    }
}
