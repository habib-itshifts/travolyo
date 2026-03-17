<?php

namespace Modules\Activity\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreActivityRequest  (Vendor panel — create)
 *
 * Validates the POST payload when a vendor creates a new activity.
 * Intentionally does NOT include status, author_id, or is_active —
 * those are admin-only fields. SaveActivityAction forces status = "pending"
 * and is_active = false at the business-logic layer.
 *
 * Admin equivalent: Modules\Activity\Http\Requests\Admin\StoreActivityRequest
 */
class StoreActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return $this->baseRules();
    }

    /**
     * Core activity rules for vendor forms.
     * Protected so UpdateActivityRequest can extend and override slug uniqueness.
     */
    protected function baseRules(): array
    {
        return [
            'title'            => ['required', 'string', 'max:255'],
            'slug'             => ['nullable', 'string', 'max:255', 'unique:activities,slug'],
            'category'         => ['nullable', 'string', 'max:100'],
            'city'             => ['nullable', 'string', 'max:100'],
            'country'          => ['nullable', 'string', 'max:100'],
            'address'          => ['nullable', 'string', 'max:255'],
            'price_per_person' => ['nullable', 'numeric', 'min:0'],
            'currency'         => ['nullable', 'string', 'max:10'],
            'max_participants' => ['nullable', 'integer', 'min:1'],
            'duration'         => ['nullable', 'string', 'max:100'],
            'description'      => ['nullable', 'string'],

            // Media
            'image_id' => ['nullable', 'integer', 'exists:media_files,id'],
            'gallery'  => ['nullable', 'string'],   // comma-separated media IDs

            // Booleans (vendor can set these; admin controls publish status)
            'instant_confirmation' => ['nullable', 'boolean'],
            'email_flyer_enabled'  => ['nullable', 'boolean'],

            // Repeater
            'extra_information'   => ['nullable', 'array'],
            'extra_information.*' => ['nullable', 'string', 'max:255'],
        ];
    }
}
