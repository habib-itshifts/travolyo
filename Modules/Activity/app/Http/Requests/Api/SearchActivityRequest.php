<?php

namespace Modules\Activity\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SearchActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'destination'           => ['required_without:city', 'string', 'max:100'],
            'city'                  => ['required_without:destination', 'string', 'max:100'],
            'category'              => ['nullable', 'string', 'max:50'],
            'activity_date'         => ['nullable', 'date', 'after_or_equal:today'],
            'participants'          => ['nullable', 'integer', 'min:1', 'max:100'],
            'price_min'             => ['nullable', 'numeric', 'min:0'],
            'price_max'             => ['nullable', 'numeric', 'gt:price_min'],
            'instant_confirmation'  => ['nullable', 'boolean'],
            'currency'              => ['nullable', 'string', 'size:3'],
            'sort_by'               => ['nullable', 'string', 'in:recommended,price_asc,price_desc'],
            'per_page'              => ['nullable', 'integer', 'between:1,100'],
            'page'                  => ['nullable', 'integer', 'min:1'],
            'provider'              => ['nullable', 'string', 'in:local'],
        ];
    }
}
