<?php

namespace Modules\Flight\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PrebookFlightRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'offer_id'     => ['required', 'string'],
            'provider'     => ['required', 'string', 'in:duffel,travolyo_b2b_xml_agency'],
            'dep_iata'     => ['required', 'string', 'size:3'],
            'arr_iata'     => ['required', 'string', 'size:3'],
            'dep_time'     => ['required', 'string'],
            'dep_date'     => ['required', 'string'],
            'arr_time'     => ['required', 'string'],
            'arr_date'     => ['required', 'string'],
            'duration'     => ['nullable', 'string'],
            'stops'        => ['required', 'integer', 'min:0'],
            'price'        => ['required', 'numeric', 'min:0'],
            'currency'     => ['required', 'string', 'size:3'],
            'airline_name' => ['nullable', 'string'],
            'airline_logo' => ['nullable', 'string'],
            'cabin_class'  => ['required', 'in:ECONOMY,PREMIUM_ECONOMY,BUSINESS,FIRST'],
            'trip_type'    => ['required', 'in:one_way,round_trip'],
            'adults'       => ['required', 'integer', 'min:1', 'max:9'],
            'children'     => ['nullable', 'integer', 'min:0', 'max:9'],
            'infants'      => ['nullable', 'integer', 'min:0', 'max:9'],
        ];
    }

    public function messages(): array
    {
        return [
            'offer_id.required'  => 'Flight offer ID is required.',
            'provider.in'        => 'Provider must be one of: duffel, travolyo_b2b_xml_agency.',
            'dep_iata.size'      => 'Departure airport must be a 3-letter IATA code.',
            'arr_iata.size'      => 'Arrival airport must be a 3-letter IATA code.',
            'cabin_class.in'     => 'Cabin class must be one of: ECONOMY, PREMIUM_ECONOMY, BUSINESS, FIRST.',
            'trip_type.in'       => 'Trip type must be one of: one_way, round_trip.',
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
