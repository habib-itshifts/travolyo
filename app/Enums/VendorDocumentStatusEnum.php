<?php

namespace App\Enums;

enum VendorDocumentStatusEnum: string
{
    case Pending  = 'pending';   // Uploaded, awaiting admin review
    case Approved = 'approved';  // Admin approved this document
    case Rejected = 'rejected';  // Admin rejected — vendor must re-upload

    public function label(): string
    {
        return match($this) {
            VendorDocumentStatusEnum::Pending  => 'Under Review',
            VendorDocumentStatusEnum::Approved => 'Approved',
            VendorDocumentStatusEnum::Rejected => 'Rejected',
        };
    }

    public function badgeColor(): string
    {
        return match($this) {
            VendorDocumentStatusEnum::Pending  => 'warning',
            VendorDocumentStatusEnum::Approved => 'success',
            VendorDocumentStatusEnum::Rejected => 'danger',
        };
    }
}
