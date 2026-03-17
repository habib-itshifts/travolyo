<?php

namespace Modules\Hotel\Http\Requests\Admin;

use Illuminate\Validation\Rule;

/**
 * UpdateHotelRoomRequest  (Admin panel — edit room)
 *
 * Extends StoreHotelRoomRequest so all base rules apply.
 * The only difference: the slug uniqueness rule ignores the room being
 * edited so its own slug does not produce a "already taken" error.
 */
class UpdateHotelRoomRequest extends StoreHotelRoomRequest
{
    public function rules(): array
    {
        // Admin resource routes use plain int IDs — cast directly.
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
