<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions per role — add more here as features are built
        $permissions = [
            'admin' => [
                // e.g. 'manage-users', 'manage-vendors', 'view-reports'
            ],
            'vendor' => [
                // e.g. 'manage-own-hotels', 'view-own-bookings'
            ],
            'customer' => [
                // e.g. 'make-booking', 'view-own-bookings'
            ],
        ];

        foreach ($permissions as $roleName => $rolePermissions) {
            $role = Role::where('name', $roleName)->first();

            if (! $role) {
                continue;
            }

            $permissionModels = collect($rolePermissions)->map(function (string $permission) {
                return Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
            });

            $role->syncPermissions($permissionModels);
        }
    }
}