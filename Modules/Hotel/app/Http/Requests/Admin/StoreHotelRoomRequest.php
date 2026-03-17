<?php

namespace Modules\Hotel\Http\Requests\Admin;

use App\Models\Currency;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * StoreHotelRoomRequest  (Admin panel — create room)
 *
 * Validates the POST payload when an admin creates a new hotel room.
 * The slug uniqueness check is scoped to the parent hotel so two different
 * hotels can have rooms with the same slug.
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
            // On create, slug must be unique within the hotel (no ignore needed).
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
    // Shared room validation rules
    // -------------------------------------------------------------------------

    /**
     * Core room rules reused by UpdateHotelRoomRequest (which overrides
     * the slug uniqueness check to ignore the record being updated).
     */
    protected function baseRules(): array
    {
        return [
            // Parent hotel — must already exist in the database.
            'hotel_id'  => ['required', 'integer', 'exists:hotels,id'],

            // Identity
            'name'      => ['required', 'string', 'max:191'],
            'room_type' => ['required', 'string', 'max:50'],

            // Currency — validated against the app's supported currency list.
            'currency'  => ['required', 'string', 'size:3', Rule::in(array_keys(Currency::supported()))],

            // Media (IDs reference the media_files table)
            'image_id' => ['nullable', 'integer', 'exists:media_files,id'],
            'gallery'  => ['nullable', 'string'],   // comma-separated media IDs

            // Bed setup: entered as free-text "king bed, sofa bed" — parsed in Action.
            'bed_configuration_text' => ['nullable', 'string', 'max:255'],

            // Capacity
            'max_adults'   => ['required', 'integer', 'min:1', 'max:20'],
            'max_children' => ['nullable', 'integer', 'min:0', 'max:20'],
            'max_occupancy'=> ['required', 'integer', 'min:1', 'max:20'],

            // Physical attributes
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
