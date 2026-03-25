<?php

namespace Modules\Hotel\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class UpdateHotelRoomRequest extends StoreHotelRoomRequest
{
    public function rules(): array
    {
        $room = $this->route('hotel_room');
        $roomId = $room instanceof \Modules\Hotel\Models\HotelRoom ? $room->id : (int) $room;

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
