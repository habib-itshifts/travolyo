<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
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
