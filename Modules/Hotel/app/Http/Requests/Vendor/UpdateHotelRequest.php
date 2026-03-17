<?php

namespace Modules\Hotel\Http\Requests\Vendor;

use Illuminate\Validation\Rule;

/**
 * UpdateHotelRequest  (Vendor panel — edit)
 *
 * Extends the vendor StoreHotelRequest so all shared rules apply.
 * The only override: slug uniqueness ignores the hotel being edited.
 *
 * Status, is_featured and sort_order are intentionally absent — the
 * SaveHotelAction enforces that vendors can never change those fields.
 */
class UpdateHotelRequest extends StoreHotelRequest
{
    public function rules(): array
    {
        // Vendor resource routes use plain int IDs — cast directly.
        $hotelId = (int) $this->route('hotel');

        return array_merge($this->baseRules(), [
            // Override slug: unique in the hotels table but ignore this hotel's row.
            'slug' => [
                'nullable',
                'string',
                'max:191',
                Rule::unique('hotels', 'slug')->ignore($hotelId),
            ],
        ]);
    }
}
