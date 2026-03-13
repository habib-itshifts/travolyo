<?php

namespace Modules\Hotel\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Hotel\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            // Free services
            ['name' => 'Room Service',         'category' => 'food',           'is_chargeable' => false, 'default_price' => null,  'price_type' => null,         'icon' => 'bell-concierge'],
            ['name' => 'Daily Housekeeping',   'category' => 'housekeeping',   'is_chargeable' => false, 'default_price' => null,  'price_type' => null,         'icon' => 'broom'],
            ['name' => 'Concierge Service',    'category' => 'concierge',      'is_chargeable' => false, 'default_price' => null,  'price_type' => null,         'icon' => 'person-booth'],
            ['name' => 'Wake-Up Call',         'category' => 'concierge',      'is_chargeable' => false, 'default_price' => null,  'price_type' => null,         'icon' => 'phone'],
            ['name' => 'Luggage Storage',      'category' => 'concierge',      'is_chargeable' => false, 'default_price' => null,  'price_type' => null,         'icon' => 'suitcase'],

            // Paid services
            ['name' => 'Airport Transfer',     'category' => 'transport',      'is_chargeable' => true,  'default_price' => 50.00, 'price_type' => 'one_time',   'icon' => 'car'],
            ['name' => 'Laundry',              'category' => 'housekeeping',   'is_chargeable' => true,  'default_price' => 15.00, 'price_type' => 'per_request', 'icon' => 'shirt'],
            ['name' => 'Dry Cleaning',         'category' => 'housekeeping',   'is_chargeable' => true,  'default_price' => 20.00, 'price_type' => 'per_request', 'icon' => 'star'],
            ['name' => 'Massage / Body Treatment', 'category' => 'wellness',   'is_chargeable' => true,  'default_price' => 80.00, 'price_type' => 'per_person', 'icon' => 'spa'],
            ['name' => 'Car Rental',           'category' => 'transport',      'is_chargeable' => true,  'default_price' => 70.00, 'price_type' => 'per_night',  'icon' => 'car-side'],
            ['name' => 'Breakfast',            'category' => 'food',           'is_chargeable' => true,  'default_price' => 20.00, 'price_type' => 'per_person', 'icon' => 'utensils'],
            ['name' => 'Half Board',           'category' => 'food',           'is_chargeable' => true,  'default_price' => 45.00, 'price_type' => 'per_person', 'icon' => 'bowl-food'],
            ['name' => 'Full Board',           'category' => 'food',           'is_chargeable' => true,  'default_price' => 70.00, 'price_type' => 'per_person', 'icon' => 'plate-wheat'],
            ['name' => 'Extra Bed',            'category' => 'housekeeping',   'is_chargeable' => true,  'default_price' => 30.00, 'price_type' => 'per_night',  'icon' => 'bed'],
            ['name' => 'Baby Crib',            'category' => 'housekeeping',   'is_chargeable' => true,  'default_price' => 10.00, 'price_type' => 'per_night',  'icon' => 'baby'],
            ['name' => 'Parking',              'category' => 'transport',      'is_chargeable' => true,  'default_price' => 15.00, 'price_type' => 'per_night',  'icon' => 'parking'],
            ['name' => 'Guided City Tour',     'category' => 'concierge',      'is_chargeable' => true,  'default_price' => 40.00, 'price_type' => 'per_person', 'icon' => 'map'],
        ];

        foreach ($services as $i => $item) {
            Service::firstOrCreate(
                ['slug' => Str::slug($item['name'])],
                array_merge($item, ['sort_order' => $i])
            );
        }
    }
}
