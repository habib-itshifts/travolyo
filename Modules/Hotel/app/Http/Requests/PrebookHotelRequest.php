<?php

namespace Modules\Hotel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrebookHotelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'offer_id'   => ['required', 'string'],
            'room_id'    => ['required', 'string'],
            'provider'   => ['required', 'string', 'in:local,travolyo_b2b_net_streaming,travolyo_b2b_local'],
            'check_in'   => ['required', 'date', 'after_or_equal:today'],
            'check_out'  => ['required', 'date', 'after:check_in'],
            'adults'     => ['required', 'integer', 'min:1', 'max:20'],
            'children'   => ['nullable', 'integer', 'min:0', 'max:10'],
            'currency'   => ['nullable', 'string', 'size:3'],

            // For checkout page display (passed through like FlightController::prebook)
            'hotel_name' => ['required', 'string'],
            'room_name'  => ['required', 'string'],
            'city'       => ['required', 'string'],
            'country'    => ['required', 'string'],
        ];
    }
}
