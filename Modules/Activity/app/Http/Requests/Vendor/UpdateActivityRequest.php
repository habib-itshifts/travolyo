<?php

namespace Modules\Activity\Http\Requests\Vendor;

use Illuminate\Validation\Rule;
use Modules\Activity\Models\Activity;

/**
 * UpdateActivityRequest  (Vendor panel — edit)
 *
 * Extends the vendor StoreActivityRequest so all shared rules apply.
 * The only override: slug uniqueness ignores the activity being edited.
 *
 * Status, author_id and is_active are intentionally absent — SaveActivityAction
 * enforces that vendors can never change those fields.
 */
class UpdateActivityRequest extends StoreActivityRequest
{
    public function rules(): array
    {
        // Route model binding resolves {activity} via slug — get the model instance.
        $activity   = $this->route('activity');
        $activityId = $activity instanceof Activity ? $activity->id : (int) $activity;

        return array_merge($this->baseRules(), [
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('activities', 'slug')->ignore($activityId),
            ],
        ]);
    }
}
