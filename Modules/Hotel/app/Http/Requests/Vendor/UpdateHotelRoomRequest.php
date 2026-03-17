<?php

namespace Modules\Hotel\Http\Requests\Vendor;

use Illuminate\Validation\Rule;

/**
 * UpdateHotelRoomRequest  (Vendor panel — edit room)
 *
 * Extends the vendor StoreHotelRoomRequest.
 * The only override: slug uniqueness ignores the room being updated.
 */
class UpdateHotelRoomRequest extends StoreHotelRoomRequest
{
    public function rules(): array
    {
        // Vendor resource routes use plain int IDs — cast directly.
        $roomId = (int) $this->route('hotel_room');

        return array_merge($this->baseRules(), [
            // Override slug: unique within the hotel but ignore this room's row.
            'slug' => [
                'nullable',
                'string',
                'max:191',
                Rule::unique('hotel_rooms', 'slug')
                    ->ignore($roomId)
                    ->where(fn ($query) => $query->where('hotel_id', $this->input('hotel_id'))),
            ],
        ]);
    }
}
