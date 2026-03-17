<?php

namespace Modules\Hotel\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class StoreHotelScrapingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url' => ['required', 'url', 'max:2048'],
        ];
    }
}
