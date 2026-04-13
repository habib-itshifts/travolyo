<?php

namespace Modules\Space\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Space\Enums\SpaceStatusEnum;
use Modules\Space\Enums\SpaceTypeEnum;

class StoreSpaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge($this->baseRules(), [
            'status'      => ['required', Rule::enum(SpaceStatusEnum::class)],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
        ]);
    }

    protected function baseRules(): array
    {
        return [
            // Identity
            'name'              => ['required', 'string', 'max:191'],
            'slug'              => ['nullable', 'string', 'max:191', 'unique:spaces,slug'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description'       => ['nullable', 'string'],
            'type'              => ['required', Rule::enum(SpaceTypeEnum::class)],

            // Capacity
            'max_guests' => ['required', 'integer', 'min:1', 'max:50'],
            'bedrooms'   => ['required', 'integer', 'min:0', 'max:20'],
            'bathrooms'  => ['required', 'integer', 'min:0', 'max:20'],
            'beds'       => ['required', 'integer', 'min:0', 'max:50'],

            // Media
            'image_id'        => ['nullable', 'integer', 'exists:media_files,id'],
            'banner_image_id' => ['nullable', 'integer', 'exists:media_files,id'],
            'gallery'         => ['nullable', 'string'],
            'video_url'       => ['nullable', 'url', 'max:2048'],

            // Location
            'address'     => ['required', 'string', 'max:255'],
            'city'        => ['required', 'string', 'max:100'],
            'state'       => ['nullable', 'string', 'max:100'],
            'country'     => ['required', 'string', 'size:2'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'latitude'    => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'   => ['nullable', 'numeric', 'between:-180,180'],

            // Contact
            'email' => ['nullable', 'email', 'max:191'],
            'phone' => ['nullable', 'string', 'max:50'],

            // Policies
            'check_in_time'          => ['nullable', 'string', 'max:5'],
            'check_out_time'         => ['nullable', 'string', 'max:5'],
            'min_day_before_booking' => ['nullable', 'integer', 'min:0'],
            'min_stay_nights'        => ['nullable', 'integer', 'min:1'],
            'max_stay_nights'        => ['nullable', 'integer', 'min:1'],
            'cancellation_policy'    => ['nullable', 'string', 'max:2000'],

            // House rules repeater
            'house_rules'           => ['nullable', 'array'],
            'house_rules.*.title'   => ['nullable', 'string', 'max:150'],
            'house_rules.*.content' => ['nullable', 'string', 'max:1000'],

            // Pricing
            'currency'        => ['required', 'string', 'max:3'],
            'price_per_night' => ['required', 'numeric', 'min:0'],
            'sale_price'      => ['nullable', 'numeric', 'min:0'],
            'cleaning_fee'    => ['nullable', 'numeric', 'min:0'],
            'service_fee'     => ['nullable', 'numeric', 'min:0'],

            // Extra prices repeater
            'extra_prices'              => ['nullable', 'array'],
            'extra_prices.*.name'       => ['nullable', 'string', 'max:150'],
            'extra_prices.*.price'      => ['nullable', 'numeric', 'min:0'],
            'extra_prices.*.type'       => ['nullable', 'in:one_time,per_day'],
            'extra_prices.*.per_person' => ['nullable'],

            // Amenities
            'amenity_ids'   => ['nullable', 'array'],
            'amenity_ids.*' => ['integer', 'exists:amenities,id'],
        ];
    }
}
