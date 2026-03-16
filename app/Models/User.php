<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes, HasApiTokens;

    protected $fillable = [
        // Identity
        'first_name',
        'last_name',
        'name',
        'username',
        'email',
        'password',
        'gender',
        'birthday',
        'avatar',
        'nationality',
        'user_type',
        // Contact
        'phone',
        'phone_country_code',
        'whatsapp_number',
        // Address
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'country',
        'zip_code',
        // Business / Vendor
        'business_name',
        'tax_number',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'user_type'         => UserType::class,
            'birthday'          => 'date',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->user_type === UserType::Admin;
    }

    public function isVendor(): bool
    {
        return $this->user_type === UserType::Vendor;
    }

    public function isCustomer(): bool
    {
        return $this->user_type === UserType::Customer;
    }
}
