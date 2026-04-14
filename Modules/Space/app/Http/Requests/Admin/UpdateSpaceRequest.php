<?php

namespace Modules\Space\Http\Requests\Admin;

use Illuminate\Validation\Rule;

class UpdateSpaceRequest extends StoreSpaceRequest
{
    protected function baseRules(): array
    {
        $rules = parent::baseRules();

        // Override slug uniqueness to ignore the current record.
        $rules['slug'] = ['nullable', 'string', 'max:191', Rule::unique('spaces', 'slug')->ignore($this->route('space'))];

        return $rules;
    }
}
