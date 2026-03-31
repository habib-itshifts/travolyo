<?php

namespace Database\Seeders;

use App\Models\TopDestination;
use Illuminate\Database\Seeder;

class TopDestinationSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'city' => 'Dubai',
                'country' => 'UAE',
                'country_code' => 'AE',
                'location' => 'DXB',
                'image_path' => 'assets/images/website/top-destination/dubai.png',
                'image_alt' => 'Dubai skyline',
                'accommodations_label' => '19,464 accommodations',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'city' => 'Abu Dhabi',
                'country' => 'UAE',
                'country_code' => 'AE',
                'location' => 'AUH',
                'image_path' => 'assets/images/website/top-destination/abu-dhabi.png',
                'image_alt' => 'Abu Dhabi',
                'accommodations_label' => '721 accommodations',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'city' => 'Sharjah',
                'country' => 'UAE',
                'country_code' => 'AE',
                'location' => 'SHJ',
                'image_path' => 'assets/images/website/top-destination/sharjah.png',
                'image_alt' => 'Sharjah',
                'accommodations_label' => '323 accommodations',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'city' => 'Ras Al Khaimah',
                'country' => 'UAE',
                'country_code' => 'AE',
                'location' => 'RKT',
                'image_path' => 'assets/images/website/top-destination/rasul-khema.png',
                'image_alt' => 'Ras Al Khaimah',
                'accommodations_label' => '398 accommodations',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'city' => 'Ajman',
                'country' => 'UAE',
                'country_code' => 'AE',
                'location' => 'QAJ',
                'image_path' => 'assets/images/website/top-destination/ajman.png',
                'image_alt' => 'Ajman beach',
                'accommodations_label' => '264 accommodations',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'city' => 'Fujairah',
                'country' => 'UAE',
                'country_code' => 'AE',
                'location' => 'FJR',
                'image_path' => 'assets/images/website/top-destination/fujairah.jpg',
                'image_alt' => 'Fujairah',
                'accommodations_label' => '210 accommodations',
                'sort_order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($items as $item) {
            TopDestination::query()->updateOrCreate(
                [
                    'city' => $item['city'],
                    'country' => $item['country'],
                ],
                $item
            );
        }
    }
}
