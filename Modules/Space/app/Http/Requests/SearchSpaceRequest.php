<?php

namespace Modules\Space\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Space\Enums\SpaceTypeEnum;
use Illuminate\Validation\Rule;

class SearchSpaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'destination' => ['required_without:city', 'string', 'max:191'],
            'city'        => ['required_without:destination', 'string', 'max:100'],
            'check_in'    => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'check_out'   => ['required', 'date_format:Y-m-d', 'after:check_in'],
            'guests'      => ['nullable', 'integer', 'min:1', 'max:50'],
            'type'        => ['nullable', Rule::enum(SpaceTypeEnum::class)],
            'price_min'   => ['nullable', 'numeric', 'min:0'],
            'price_max'   => ['nullable', 'numeric', 'min:0'],
            'bedrooms'    => ['nullable', 'integer', 'min:0'],
            'bathrooms'   => ['nullable', 'integer', 'min:0'],
            'amenities'   => ['nullable', 'array'],
            'amenities.*' => ['integer'],
            'currency'    => ['nullable', 'string', 'size:3'],
            'sort_by'     => ['nullable', 'in:price_asc,price_desc,newest'],
            'per_page'    => ['nullable', 'integer', 'min:1', 'max:100'],
            'page'        => ['nullable', 'integer', 'min:1'],
        ];
    }
}
