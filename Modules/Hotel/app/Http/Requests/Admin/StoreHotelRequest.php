<?php

namespace Modules\Hotel\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Hotel\Enums\HotelStatusEnum;

/**
 * StoreHotelRequest  (Admin panel — create)
 *
 * Validates the POST payload when an admin creates a new hotel.
 * Merges the shared base rules (defined in this class) with the
 * admin-only publish controls: status, is_featured, sort_order.
 *
 * Vendor equivalent: Modules\Hotel\Http\Requests\Vendor\StoreHotelRequest
 */
class StoreHotelRequest extends FormRequest
{
    /** All authenticated admins are authorised to create hotels. */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge($this->baseRules(), [
            // Admin-only publish controls — vendors never set these directly.
            // Rule::enum() validates against HotelStatusEnum::cases() so adding
            // a new status value to the enum is enough; no rule string to update.
            'status'      => ['required', Rule::enum(HotelStatusEnum::class)],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
        ]);
    }

    // -------------------------------------------------------------------------
    // Shared hotel validation rules
    // -------------------------------------------------------------------------

    /**
     * Core hotel rules reused by UpdateHotelRequest (which overrides the
     * slug uniqueness check to ignore the record being updated).
     *
     * Kept protected so UpdateHotelRequest can call parent::baseRules() and
     * only change the slug rule.
     */
    protected function baseRules(): array
    {
        return [
            // Identity
            'name'              => ['required', 'string', 'max:191'],
            'slug'              => ['nullable', 'string', 'max:191', 'unique:hotels,slug'],
            'star_rating'       => ['nullable', 'integer', 'between:1,5'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description'       => ['nullable', 'string'],

            // Media (IDs reference the media_files table)
            'image_id'        => ['nullable', 'integer', 'exists:media_files,id'],
            'banner_image_id' => ['nullable', 'integer', 'exists:media_files,id'],
            'gallery'         => ['nullable', 'string'],   // comma-separated media IDs
            'video_url'       => ['nullable', 'url', 'max:2048'],

            // Location
            'address'     => ['required', 'string', 'max:255'],
            'city'        => ['required', 'string', 'max:100'],
            'state'       => ['nullable', 'string', 'max:100'],
            'country'     => ['required', 'string', 'size:2'],   // ISO 3166-1 alpha-2
            'postal_code' => ['nullable', 'string', 'max:20'],
            'latitude'    => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'   => ['nullable', 'numeric', 'between:-180,180'],

            // Contact
            'email'   => ['nullable', 'email', 'max:191'],
            'phone'   => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'url', 'max:255'],

            // Stay policies & pricing
            'check_in_time'          => ['nullable', 'string', 'max:5'],
            'check_out_time'         => ['nullable', 'string', 'max:5'],
            'base_price'             => ['nullable', 'numeric', 'min:0'],
            'sale_price'             => ['nullable', 'numeric', 'min:0'],
            'min_day_before_booking' => ['nullable', 'integer', 'min:0'],
            'min_day_stays'          => ['nullable', 'integer', 'min:0'],
            'related_hotel_ids'      => ['nullable', 'string', 'max:255'],
            'payment_methods'        => ['nullable', 'array'],
            'languages_spoken'       => ['nullable', 'array'],

            // Repeater: hotel policies (check-in rules, cancellation, etc.)
            'policies'           => ['nullable', 'array'],
            'policies.*.title'   => ['nullable', 'string', 'max:150'],
            'policies.*.content' => ['nullable', 'string', 'max:1000'],

            // Repeater: points of interest near the hotel
            'nearby_places'           => ['nullable', 'array'],
            'nearby_places.*.name'    => ['nullable', 'string', 'max:150'],
            'nearby_places.*.content' => ['nullable', 'string', 'max:500'],
            'nearby_places.*.value'   => ['nullable', 'numeric', 'min:0'],
            'nearby_places.*.type'    => ['nullable', 'in:m,km'],

            // Repeater: optional extra charges (resort fee, parking, etc.)
            'extra_prices'              => ['nullable', 'array'],
            'extra_prices.*.name'       => ['nullable', 'string', 'max:150'],
            'extra_prices.*.price'      => ['nullable', 'numeric', 'min:0'],
            'extra_prices.*.type'       => ['nullable', 'in:one_time,per_day'],
            'extra_prices.*.per_person' => ['nullable'],

            // Pivot relationships
            'amenity_ids'   => ['nullable', 'array'],
            'amenity_ids.*' => ['integer', 'exists:amenities,id'],
            'service_ids'   => ['nullable', 'array'],
            'service_ids.*' => ['integer', 'exists:services,id'],
        ];
    }
}
