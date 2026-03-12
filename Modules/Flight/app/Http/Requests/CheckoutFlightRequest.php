<?php

namespace Modules\Flight\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CheckoutFlightRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Cache token from prebook step
            'checkout_token'   => ['required', 'string'],

            // Payment gateway
            'payment_gateway'  => ['required', 'in:stripe,ngenius'],

            // Contact
            'contact_email'    => ['required', 'email'],
            'contact_phone'    => ['required', 'string', 'min:7'],

            // Passengers array
            'passengers'                       => ['required', 'array', 'min:1'],
            'passengers.*.first_name'          => ['required', 'string'],
            'passengers.*.last_name'           => ['required', 'string'],
            'passengers.*.title'               => ['required', 'in:Mr,Mrs,Ms,Dr'],
            'passengers.*.dob'                 => ['required', 'date'],
            'passengers.*.gender'              => ['required', 'in:M,F'],
            'passengers.*.nationality'         => ['required', 'string', 'size:2'],
            'passengers.*.passport'            => ['required', 'string'],
            'passengers.*.passport_expiry'     => ['required', 'date', 'after:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_gateway.in'             => 'Payment gateway must be one of: stripe, ngenius.',
            'passengers.*.nationality.size'  => 'Nationality must be a 2-letter country code (e.g. AE, US).',
            'passengers.*.passport_expiry.after' => 'Passport must not be expired.',
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}
