<?php

namespace Modules\Activity\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Activity\Enums\ActivityStatusEnum;

/**
 * StoreActivityRequest  (Admin panel — create)
 *
 * Validates the POST payload when an admin creates a new activity.
 * Includes admin-only fields: status, author_id, is_active.
 *
 * Vendor equivalent: Modules\Activity\Http\Requests\Vendor\StoreActivityRequest
 */
class StoreActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array_merge($this->baseRules(), [
            // Admin-only controls — vendors never set these directly.
            'status'    => ['required', Rule::enum(ActivityStatusEnum::class)],
            'author_id' => ['nullable', 'integer', 'exists:users,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    // -------------------------------------------------------------------------
    // Shared activity validation rules
    // -------------------------------------------------------------------------

    /**
     * Core rules reused by UpdateActivityRequest (which overrides slug uniqueness).
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

            // Booleans
            'instant_confirmation' => ['nullable', 'boolean'],
            'email_flyer_enabled'  => ['nullable', 'boolean'],

            // Repeater
            'extra_information'   => ['nullable', 'array'],
            'extra_information.*' => ['nullable', 'string', 'max:255'],
        ];
    }
}
