<?php

namespace Modules\Activity\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'checkout_token'              => ['required', 'string'],
            'contact_email'               => ['required', 'email'],
            'contact_phone'               => ['required', 'string', 'max:50'],
            'payment_gateway'             => ['required', 'string'],
            'special_requests'            => ['nullable', 'string', 'max:1000'],
            'passengers'                  => ['required', 'array', 'min:1'],
            'passengers.*.first_name'     => ['required', 'string', 'max:100'],
            'passengers.*.last_name'      => ['required', 'string', 'max:100'],
            'passengers.*.title'          => ['nullable', 'string', 'max:20'],
            'passengers.*.dob'            => ['required', 'date', 'before_or_equal:today'],
            'passengers.*.nationality'    => ['required', 'string', 'max:5'],
            'passengers.*.gender'         => ['required', 'in:M,F'],
            'passengers.*.passport'       => ['required', 'string', 'max:50'],
            'passengers.*.passport_expiry'=> ['required', 'date', 'after_or_equal:today'],
        ];
    }
}
