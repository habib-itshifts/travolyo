<?php

namespace Modules\Hotel\Http\Requests\Vendor;

use Illuminate\Validation\Rule;

class UpdateHotelRoomRequest extends StoreHotelRoomRequest
{
    public function rules(): array
    {
        $roomId = (int) $this->route('hotel_room');

        return array_merge($this->baseRules(), [
            'room_type_id' => [
                'required',
                'integer',
                'exists:room_types,id',
                Rule::unique('hotel_rooms')
                    ->ignore($roomId)
                    ->where(fn ($query) => $query->where('hotel_id', $this->input('hotel_id'))),
            ],
        ]);
    }
}
