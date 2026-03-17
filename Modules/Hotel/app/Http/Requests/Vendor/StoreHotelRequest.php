<?php

namespace Modules\Hotel\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreHotelRequest  (Vendor panel — create)
 *
 * Validates the POST payload when a vendor creates a new hotel.
 * Intentionally does NOT include status, is_featured, or sort_order —
 * those are admin-only fields. The SaveHotelAction enforces status = "draft"
 * at the business-logic layer regardless of what is submitted.
 *
 * Admin equivalent: Modules\Hotel\Http\Requests\Admin\StoreHotelRequest
 */
class StoreHotelRequest extends FormRequest
{
    /** All authenticated vendors are authorised to create hotels. */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return $this->baseRules();
    }

    // -------------------------------------------------------------------------
    // Shared hotel validation rules (vendor subset — no publish controls)
    // -------------------------------------------------------------------------

    /**
     * Core hotel rules for vendor forms.
     * Protected so UpdateHotelRequest can extend and override slug uniqueness.
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

            // Repeater: hotel policies
            'policies'           => ['nullable', 'array'],
            'policies.*.title'   => ['nullable', 'string', 'max:150'],
            'policies.*.content' => ['nullable', 'string', 'max:1000'],

            // Repeater: nearby places / points of interest
            'nearby_places'           => ['nullable', 'array'],
            'nearby_places.*.name'    => ['nullable', 'string', 'max:150'],
            'nearby_places.*.content' => ['nullable', 'string', 'max:500'],
            'nearby_places.*.value'   => ['nullable', 'numeric', 'min:0'],
            'nearby_places.*.type'    => ['nullable', 'in:m,km'],

            // Repeater: optional extra charges
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
