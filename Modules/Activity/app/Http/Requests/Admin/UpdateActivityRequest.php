<?php

namespace Modules\Activity\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Modules\Activity\Models\Activity;

/**
 * UpdateActivityRequest  (Admin panel — edit)
 *
 * Extends StoreActivityRequest so all shared rules apply.
 * The only override: slug uniqueness ignores the activity being edited.
 */
class UpdateActivityRequest extends StoreActivityRequest
{
    public function rules(): array
    {
        // Route model binding resolves {activity} via slug — get the model instance.
        $activity   = $this->route('activity');
        $activityId = $activity instanceof Activity ? $activity->id : (int) $activity;

        return array_merge(parent::rules(), [
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('activities', 'slug')->ignore($activityId),
            ],
        ]);
    }
}