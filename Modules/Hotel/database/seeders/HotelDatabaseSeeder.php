<?php

namespace Modules\Hotel\Database\Seeders;

use Illuminate\Database\Seeder;

class HotelDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            AmenitySeeder::class,
            ServiceSeeder::class,
        ]);
    }
}
