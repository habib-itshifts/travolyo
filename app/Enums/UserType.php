<?php

namespace App\Enums;

enum UserType: string
{
    case Admin    = 'admin';
    case Vendor   = 'vendor';
    case Customer = 'customer';

    public function label(): string
    {
        return match($this) {
            UserType::Admin    => 'Admin',
            UserType::Vendor   => 'Vendor',
            UserType::Customer => 'Customer',
        };
    }

    public function redirectRoute(): string
    {
        return match($this) {
            UserType::Admin    => 'admin.dashboard',
            UserType::Vendor   => 'vendor.dashboard',
            UserType::Customer => 'customer.dashboard',
        };
    }
}
