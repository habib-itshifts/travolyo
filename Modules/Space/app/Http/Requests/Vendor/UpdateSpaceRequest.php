<?php

namespace Modules\Space\Http\Requests\Vendor;

use Illuminate\Validation\Rule;
use Modules\Space\Http\Requests\Admin\StoreSpaceRequest;

class UpdateSpaceRequest extends StoreSpaceRequest
{
    public function rules(): array
    {
        return $this->baseRules();
    }

    protected function baseRules(): array
    {
        $rules = parent::baseRules();

        $rules['slug'] = ['nullable', 'string', 'max:191', Rule::unique('spaces', 'slug')->ignore($this->route('space'))];

        return $rules;
    }
}
