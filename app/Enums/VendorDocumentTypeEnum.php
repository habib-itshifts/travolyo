<?php

namespace App\Enums;

enum VendorDocumentTypeEnum: string
{
    case NationalId     = 'national_id';      // Government-issued national ID card
    case Passport       = 'passport';         // Passport copy
    case BusinessReg    = 'business_reg';     // Business / company registration certificate
    case TaxCert        = 'tax_cert';         // Tax registration / VAT certificate
    case BankStatement  = 'bank_statement';   // Bank statement for financial verification

    public function label(): string
    {
        return match($this) {
            VendorDocumentTypeEnum::NationalId    => 'National ID Card',
            VendorDocumentTypeEnum::Passport      => 'Passport',
            VendorDocumentTypeEnum::BusinessReg   => 'Business Registration',
            VendorDocumentTypeEnum::TaxCert       => 'Tax / VAT Certificate',
            VendorDocumentTypeEnum::BankStatement => 'Bank Statement',
        };
    }

    public function isRequired(): bool
    {
        return match($this) {
            VendorDocumentTypeEnum::NationalId,
            VendorDocumentTypeEnum::BusinessReg => true,
            default                             => false,
        };
    }
}
