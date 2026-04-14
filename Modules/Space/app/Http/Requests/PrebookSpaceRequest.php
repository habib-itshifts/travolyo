<?php

namespace Modules\Space\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrebookSpaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'space_id'  => ['required', 'integer', 'exists:spaces,id'],
            'check_in'  => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'check_out' => ['required', 'date_format:Y-m-d', 'after:check_in'],
            'adults'    => ['required', 'integer', 'min:1'],
            'children'  => ['nullable', 'integer', 'min:0'],
            'infants'   => ['nullable', 'integer', 'min:0'],
            'currency'  => ['nullable', 'string', 'size:3'],
        ];
    }
}
