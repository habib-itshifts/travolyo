<?php

namespace Modules\Hotel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchHotelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'city'        => ['required', 'string', 'max:100'],
            'check_in'    => ['required', 'date', 'after_or_equal:today'],
            'check_out'   => ['required', 'date', 'after:check_in'],
            'adults'      => ['required', 'integer', 'min:1', 'max:20'],
            'children'    => ['nullable', 'integer', 'min:0', 'max:10'],
            'rooms'       => ['nullable', 'integer', 'min:1', 'max:10'],
            'star_rating' => ['nullable', 'integer', 'between:1,5'],
            'price_min'   => ['nullable', 'numeric', 'min:0'],
            'price_max'   => ['nullable', 'numeric', 'gt:price_min'],
            'amenity_ids' => ['nullable', 'array'],
            'amenity_ids.*'=> ['integer', 'exists:amenities,id'],
            'currency'    => ['nullable', 'string', 'size:3'],
            'sort_by'     => ['nullable', 'string', 'in:price_asc,price_desc,rating_desc,featured'],
            'per_page'    => ['nullable', 'integer', 'between:1,100'],
            'provider'    => ['nullable', 'string', 'in:local,travolyo_b2b,hyperguest'],
        ];
    }
}
