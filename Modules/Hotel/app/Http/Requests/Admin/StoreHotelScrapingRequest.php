<?php

namespace Modules\Hotel\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Hotel\Enums\HotelStatusEnum;

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
            'status' => ['required', Rule::enum(HotelStatusEnum::class)],
        ];
    }
}
