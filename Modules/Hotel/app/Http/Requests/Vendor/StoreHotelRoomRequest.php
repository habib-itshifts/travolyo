<?php

namespace Modules\Hotel\Http\Requests\Vendor;

use App\Models\Currency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * StoreHotelRoomRequest  (Vendor panel — create room)
 *
 * Validates the POST payload when a vendor creates a new hotel room.
 * The hotel_id is scoped to the vendor's own hotels in the controller
 * before this request is resolved, so we simply require it to exist.
 */
class StoreHotelRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge($this->baseRules(), [
            // On create, slug must be unique within the hotel (no ignore clause).
            'slug' => [
                'nullable',
                'string',
                'max:191',
                Rule::unique('hotel_rooms', 'slug')
                    ->where(fn ($query) => $query->where('hotel_id', $this->input('hotel_id'))),
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // Shared room rules (vendor subset)
    // -------------------------------------------------------------------------

    /**
     * Core room rules.
     * Protected so UpdateHotelRoomRequest can extend and override slug rule.
     */
    protected function baseRules(): array
    {
        return [
            // Parent hotel — must exist and will be ownership-checked in the controller.
            'hotel_id'  => ['required', 'integer', 'exists:hotels,id'],

            // Identity
            'name'      => ['required', 'string', 'max:191'],
            'room_type' => ['required', 'string', 'max:50'],

            // Currency validated against the supported list.
            'currency'  => ['required', 'string', 'size:3', Rule::in(array_keys(Currency::supported()))],

            // Media
            'image_id' => ['nullable', 'integer', 'exists:media_files,id'],
            'gallery'  => ['nullable', 'string'],   // comma-separated media IDs

            // Bed setup: free-text, parsed to array by SaveHotelRoomAction.
            'bed_configuration_text' => ['nullable', 'string', 'max:255'],

            // Capacity
            'max_adults'   => ['required', 'integer', 'min:1', 'max:20'],
            'max_children' => ['nullable', 'integer', 'min:0', 'max:20'],
            'max_occupancy'=> ['required', 'integer', 'min:1', 'max:20'],

            // Physical
            'size_sqm'   => ['nullable', 'numeric', 'min:0'],
            'floor'      => ['nullable', 'string', 'max:20'],
            'view_type'  => ['nullable', 'string', 'max:50'],
            'description'=> ['nullable', 'string'],

            // Pricing
            'base_price'        => ['required', 'numeric', 'min:0'],
            'extra_adult_price' => ['nullable', 'numeric', 'min:0'],
            'extra_child_price' => ['nullable', 'numeric', 'min:0'],

            // Inventory & display
            'quantity'   => ['required', 'integer', 'min:1'],
            'is_active'  => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],

            // Amenity pivot
            'amenity_ids'   => ['nullable', 'array'],
            'amenity_ids.*' => ['integer', 'exists:amenities,id'],
        ];
    }
}
