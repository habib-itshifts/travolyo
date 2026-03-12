<?php

namespace Modules\Flight\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Flight\Enums\FlightProviderEnum;

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
            'provider'       => ['required', 'in:' . implode(',', array_column(FlightProviderEnum::cases(), 'value'))],
        ];
    }

    public function messages(): array
    {
        return [
            'origin.size'           => 'Origin must be a 3-letter IATA code.',
            'destination.size'      => 'Destination must be a 3-letter IATA code.',
            'destination.different' => 'Origin and destination must be different.',
            'return_date.after'     => 'Return date must be after the departure date.',
        ];
    }
}