<?php

namespace Modules\Hotel\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Hotel\Models\Amenity;

class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        $amenities = [
            // Hotel-level amenities
            ['name' => 'Free WiFi',           'category' => 'connectivity',  'applies_to' => 'both',  'icon' => 'wifi'],
            ['name' => 'Swimming Pool',        'category' => 'recreation',    'applies_to' => 'hotel', 'icon' => 'pool'],
            ['name' => 'Fitness Center',       'category' => 'wellness',      'applies_to' => 'hotel', 'icon' => 'dumbbell'],
            ['name' => 'Spa',                  'category' => 'wellness',      'applies_to' => 'hotel', 'icon' => 'spa'],
            ['name' => 'Restaurant',           'category' => 'food_drink',    'applies_to' => 'hotel', 'icon' => 'utensils'],
            ['name' => 'Bar / Lounge',         'category' => 'food_drink',    'applies_to' => 'hotel', 'icon' => 'wine-glass'],
            ['name' => 'Free Parking',         'category' => 'transport',     'applies_to' => 'hotel', 'icon' => 'parking'],
            ['name' => 'Airport Shuttle',      'category' => 'transport',     'applies_to' => 'hotel', 'icon' => 'shuttle-van'],
            ['name' => 'Business Center',      'category' => 'business',      'applies_to' => 'hotel', 'icon' => 'briefcase'],
            ['name' => 'Conference Room',      'category' => 'business',      'applies_to' => 'hotel', 'icon' => 'presentation'],
            ['name' => '24-Hour Front Desk',   'category' => 'safety',        'applies_to' => 'hotel', 'icon' => 'clock'],
            ['name' => 'CCTV / Security',      'category' => 'safety',        'applies_to' => 'hotel', 'icon' => 'shield'],
            ['name' => 'Kids Club',            'category' => 'recreation',    'applies_to' => 'hotel', 'icon' => 'child'],
            ['name' => 'Beach Access',         'category' => 'recreation',    'applies_to' => 'hotel', 'icon' => 'umbrella-beach'],
            ['name' => 'Rooftop Terrace',      'category' => 'recreation',    'applies_to' => 'hotel', 'icon' => 'building'],
            ['name' => 'Pet Friendly',         'category' => 'general',       'applies_to' => 'hotel', 'icon' => 'paw'],
            ['name' => 'EV Charging',          'category' => 'transport',     'applies_to' => 'hotel', 'icon' => 'bolt'],
            ['name' => 'Breakfast Included',   'category' => 'food_drink',    'applies_to' => 'hotel', 'icon' => 'coffee'],

            // Room-level amenities
            ['name' => 'Air Conditioning',     'category' => 'general',       'applies_to' => 'room',  'icon' => 'snowflake'],
            ['name' => 'Flat-screen TV',        'category' => 'general',       'applies_to' => 'room',  'icon' => 'tv'],
            ['name' => 'Mini Bar',             'category' => 'food_drink',    'applies_to' => 'room',  'icon' => 'wine-bottle'],
            ['name' => 'Safe',                 'category' => 'safety',        'applies_to' => 'room',  'icon' => 'lock'],
            ['name' => 'Bathtub',              'category' => 'wellness',      'applies_to' => 'room',  'icon' => 'bath'],
            ['name' => 'Rain Shower',          'category' => 'wellness',      'applies_to' => 'room',  'icon' => 'shower'],
            ['name' => 'Balcony',              'category' => 'general',       'applies_to' => 'room',  'icon' => 'door-open'],
            ['name' => 'Kitchenette',          'category' => 'food_drink',    'applies_to' => 'room',  'icon' => 'sink'],
            ['name' => 'Coffee Machine',       'category' => 'food_drink',    'applies_to' => 'room',  'icon' => 'mug-hot'],
            ['name' => 'Blackout Curtains',    'category' => 'general',       'applies_to' => 'room',  'icon' => 'moon'],
            ['name' => 'Iron & Ironing Board', 'category' => 'general',       'applies_to' => 'room',  'icon' => 'shirt'],
            ['name' => 'Hair Dryer',           'category' => 'wellness',      'applies_to' => 'room',  'icon' => 'wind'],
            ['name' => 'Desk / Work Area',     'category' => 'business',      'applies_to' => 'room',  'icon' => 'laptop'],
        ];

        foreach ($amenities as $i => $item) {
            Amenity::firstOrCreate(
                ['slug' => Str::slug($item['name'])],
                array_merge($item, ['sort_order' => $i])
            );
        }
    }
}
