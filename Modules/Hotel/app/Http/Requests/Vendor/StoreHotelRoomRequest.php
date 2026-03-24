<?php

namespace Modules\Hotel\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreHotelRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge($this->baseRules(), [
            'room_type_id' => [
                'required',
                'integer',
                'exists:room_types,id',
                Rule::unique('hotel_rooms')
                    ->where(fn ($query) => $query->where('hotel_id', $this->input('hotel_id'))),
            ],
        ]);
    }

    protected function baseRules(): array
    {
        return [
            'hotel_id'   => ['required', 'integer', 'exists:hotels,id'],
            'image_id'   => ['nullable', 'integer', 'exists:media_files,id'],
            'gallery'    => ['nullable', 'string'],
            'floor'      => ['nullable', 'string', 'max:20'],
            'quantity'   => ['required', 'integer', 'min:1'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'is_active'  => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'amenity_ids'   => ['nullable', 'array'],
            'amenity_ids.*' => ['integer', 'exists:amenities,id'],
        ];
    }
}
