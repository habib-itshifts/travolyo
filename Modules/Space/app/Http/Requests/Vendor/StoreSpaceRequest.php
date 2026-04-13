<?php

namespace Modules\Space\Http\Requests\Vendor;

use Modules\Space\Http\Requests\Admin\StoreSpaceRequest as AdminStoreSpaceRequest;

/**
 * Vendor StoreSpaceRequest
 *
 * Inherits all base rules from the Admin request but omits
 * status, is_featured, and sort_order fields.
 * SaveSpaceAction enforces status="draft" for vendors.
 */
class StoreSpaceRequest extends AdminStoreSpaceRequest
{
    public function rules(): array
    {
        return $this->baseRules();
    }
}
