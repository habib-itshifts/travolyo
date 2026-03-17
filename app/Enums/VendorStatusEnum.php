<?php

namespace App\Enums;

enum VendorStatusEnum: string
{
    case Pending       = 'pending';        // Request submitted, awaiting admin approval
    case Approved      = 'approved';       // Admin approved, vendor portal unlocked, awaiting documents
    case DocsSubmitted = 'docs_submitted'; // Documents uploaded, awaiting admin review
    case Verified      = 'verified';       // Fully verified — can list hotels
    case Rejected      = 'rejected';       // Rejected at any step

    public function label(): string
    {
        return match($this) {
            VendorStatusEnum::Pending       => 'Pending Approval',
            VendorStatusEnum::Approved      => 'Upload Documents',
            VendorStatusEnum::DocsSubmitted => 'Documents Under Review',
            VendorStatusEnum::Verified      => 'Verified',
            VendorStatusEnum::Rejected      => 'Rejected',
        };
    }

    public function badgeColor(): string
    {
        return match($this) {
            VendorStatusEnum::Pending       => 'warning',
            VendorStatusEnum::Approved      => 'info',
            VendorStatusEnum::DocsSubmitted => 'primary',
            VendorStatusEnum::Verified      => 'success',
            VendorStatusEnum::Rejected      => 'danger',
        };
    }
}
