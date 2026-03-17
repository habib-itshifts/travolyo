<?php

namespace Modules\Hotel\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Modules\Hotel\Enums\HotelStatusEnum;

/**
 * UpdateHotelRequest  (Admin panel — edit)
 *
 * Extends StoreHotelRequest so all the same base rules apply.
 * The only difference from the store request is the slug uniqueness rule:
 * on update we must ignore the hotel being edited so its own slug does not
 * trigger a "already taken" error.
 *
 * The hotel ID is resolved from the route parameter {hotel}.
 */
class UpdateHotelRequest extends StoreHotelRequest
{
    public function rules(): array
    {
        // Admin resource routes use plain int IDs (no route-model binding),
        // so the 'hotel' segment is always a raw integer string — cast directly.
        $hotelId = (int) $this->route('hotel');

        return array_merge($this->baseRules(), [
            // Override slug: unique but ignore the current hotel's own slug.
            'slug' => ['nullable', 'string', 'max:191', Rule::unique('hotels', 'slug')->ignore($hotelId)],

            // Admin-only publish controls (same as store).
            'status'      => ['required', Rule::enum(HotelStatusEnum::class)],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
