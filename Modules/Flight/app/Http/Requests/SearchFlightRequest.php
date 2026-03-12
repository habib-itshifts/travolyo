<?php

namespace Modules\Flight\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SearchFlightRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'origin'         => ['required', 'string', 'size:3'],
            'destination'    => ['required', 'string', 'size:3', 'different:origin'],
            'departure_date' => ['required', 'date', 'after_or_equal:today'],
            'return_date'    => ['nullable', 'date', 'after:departure_date'],
            'trip_type'      => ['required', 'in:one_way,round_trip'],
            'adults'         => ['required', 'integer', 'min:1', 'max:9'],
            'children'       => ['nullable', 'integer', 'min:0', 'max:9'],
            'infants'        => ['nullable', 'integer', 'min:0', 'max:9'],
            'cabin_class'    => ['required', 'in:ECONOMY,PREMIUM_ECONOMY,BUSINESS,FIRST'],
        ];
    }

    public function messages(): array
    {
        return [
            'origin.required'       => 'Origin airport code is required (e.g., DXB).',
            'origin.size'           => 'Origin must be a 3-letter airport code (e.g., DXB).',

            'destination.required'  => 'Destination airport code is required (e.g., LHR).',
            'destination.size'      => 'Destination must be a 3-letter airport code (e.g., LHR).',
            'destination.different' => 'Origin and destination cannot be the same.',

            'departure_date.required' => 'Departure date is required (YYYY-MM-DD).',
            'departure_date.after_or_equal' => 'Departure date must be today or later.',

            'return_date.after'     => 'Return date must be after the departure date.',

            'trip_type.required'    => 'Trip type is required.',
            'trip_type.in'          => 'Trip type must be one_way or round_trip.',

            'adults.required'       => 'Number of adults is required (1–9).',

            'cabin_class.required'  => 'Cabin class is required.',
            'cabin_class.in'        => 'Cabin class must be ECONOMY, PREMIUM_ECONOMY, BUSINESS, or FIRST.',
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