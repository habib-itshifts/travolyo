<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'      => 'Admin',
                'first_name' => 'Admin',
                'email'     => 'admin@travolyo.com',
                'password'  => Hash::make('12345678'),
                'user_type' => UserType::Admin,

            ],
            [
                'name'      => 'Vendor',
                'first_name' => 'Vendor',
                'email'     => 'vendor@travolyo.com',
                'password'  => Hash::make('12345678'),
                'user_type' => UserType::Vendor,
                'vendor_status' => 'verified',
            ],
            [
                'name'      => 'Customer',
                'first_name' => 'Customer',
                'email'     => 'customer@travolyo.com',
                'password'  => Hash::make('12345678'),
                'user_type' => UserType::Customer,
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                $data
            );

            $user->syncRoles([$data['user_type']->value]);
        }
    }
}
