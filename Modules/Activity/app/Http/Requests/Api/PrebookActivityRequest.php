<?php

namespace Modules\Activity\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class PrebookActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'offer_id'       => ['required', 'string'],
            'provider'       => ['required', 'string', 'in:local'],
            'activity_date'  => ['required', 'date', 'after_or_equal:today'],
            'participants'   => ['required', 'integer', 'min:1', 'max:100'],
            'currency'       => ['nullable', 'string', 'size:3'],

            // Display data passed through to checkout page
            'activity_title' => ['required', 'string'],
            'city'           => ['nullable', 'string'],
            'country'        => ['nullable', 'string'],
            'category'       => ['nullable', 'string'],
            'duration'       => ['nullable', 'string'],
        ];
    }
}
