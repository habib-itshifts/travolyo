<?php

namespace Modules\Hotel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutHotelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'checkout_token'  => ['required', 'string'],
            'first_name'      => ['required', 'string', 'max:100'],
            'last_name'       => ['required', 'string', 'max:100'],
            'email'           => ['required', 'email'],
            'phone'           => ['required', 'string', 'max:50'],
            'payment_gateway' => ['required', 'string'],
            'special_requests'=> ['nullable', 'string', 'max:1000'],
            'extra_services'  => ['nullable', 'array'],
        ];
    }
}
