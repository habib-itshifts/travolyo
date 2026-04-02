<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Activity\Database\Seeders\ActivitySeeder;
use Modules\Hotel\Database\Seeders\HotelDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Core
            RoleSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,

            // Modules - Hotel
            HotelDatabaseSeeder::class,

            // Modules - Activity
            ActivitySeeder::class,

            // Website
            TopDestinationSeeder::class,

        ]);
    }
}
