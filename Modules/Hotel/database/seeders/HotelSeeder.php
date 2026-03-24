<?php

namespace Modules\Hotel\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelRoom;
use Modules\Hotel\Models\RoomType;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        $vendor = User::where('email', 'vendor@travolyo.com')->first();

        // Create global room types
        $roomTypes = [
            [
                'name'              => 'Deluxe King Room',
                'slug'              => 'deluxe-king-room',
                'bed_configuration' => ['king' => 1],
                'max_adults'        => 2,
                'max_children'      => 1,
                'max_occupancy'     => 3,
                'size_sqm'          => 38.00,
                'view_type'         => 'city',
                'description'       => 'Spacious king room with premium furnishings.',
                'extra_adult_price' => 50.00,
                'extra_child_price' => 25.00,
                'is_active'         => true,
                'sort_order'        => 1,
            ],
            [
                'name'              => 'Executive Suite',
                'slug'              => 'executive-suite',
                'bed_configuration' => ['king' => 1, 'sofa_bed' => 1],
                'max_adults'        => 2,
                'max_children'      => 2,
                'max_occupancy'     => 4,
                'size_sqm'          => 72.00,
                'view_type'         => 'sea',
                'description'       => 'Elegant suite with separate living area.',
                'extra_adult_price' => 80.00,
                'extra_child_price' => 30.00,
                'is_active'         => true,
                'sort_order'        => 2,
            ],
            [
                'name'              => 'Standard Twin Room',
                'slug'              => 'standard-twin-room',
                'bed_configuration' => ['twin' => 2],
                'max_adults'        => 2,
                'max_children'      => 0,
                'max_occupancy'     => 2,
                'size_sqm'          => 28.00,
                'view_type'         => 'garden',
                'description'       => 'Comfortable twin room for budget-conscious travellers.',
                'extra_adult_price' => 0.00,
                'extra_child_price' => 0.00,
                'is_active'         => true,
                'sort_order'        => 3,
            ],
            [
                'name'              => 'Family Beach Suite',
                'slug'              => 'family-beach-suite',
                'bed_configuration' => ['king' => 1, 'twin' => 2],
                'max_adults'        => 2,
                'max_children'      => 3,
                'max_occupancy'     => 5,
                'size_sqm'          => 85.00,
                'view_type'         => 'sea',
                'description'       => 'Spacious family suite with direct sea views.',
                'extra_adult_price' => 60.00,
                'extra_child_price' => 20.00,
                'is_active'         => true,
                'sort_order'        => 4,
            ],
        ];

        $createdTypes = [];
        foreach ($roomTypes as $rtData) {
            $rt = RoomType::firstOrCreate(
                ['slug' => $rtData['slug']],
                array_merge($rtData, ['user_id' => $vendor?->id])
            );
            $createdTypes[$rt->slug] = $rt;
        }

        // Hotels with rooms
        $hotels = [
            [
                'hotel' => [
                    'author_id'         => $vendor?->id,
                    'name'              => 'Grand Palace Hotel',
                    'slug'              => 'grand-palace-hotel',
                    'star_rating'       => 5,
                    'description'       => 'A luxurious 5-star hotel in the heart of the city.',
                    'short_description' => 'Luxury 5-star city-centre hotel.',
                    'address'           => '123 King Street',
                    'city'              => 'Dubai',
                    'country'           => 'AE',
                    'postal_code'       => '00000',
                    'latitude'          => 25.20480,
                    'longitude'         => 55.27080,
                    'email'             => 'info@grandpalace.com',
                    'phone'             => '+971-4-000-0001',
                    'check_in_time'     => '15:00',
                    'check_out_time'    => '12:00',
                    'currency'          => 'USD',
                    'payment_methods'   => json_encode(['cash', 'card', 'bank_transfer']),
                    'languages_spoken'  => json_encode(['en', 'ar']),
                    'status'            => 'active',
                    'is_featured'       => true,
                    'sort_order'        => 1,
                    'base_price'        => 350.00,
                    'sale_price'        => 299.00,
                ],
                'rooms' => [
                    ['room_type_slug' => 'deluxe-king-room',  'floor' => '5',  'quantity' => 10, 'base_price' => 299.00],
                    ['room_type_slug' => 'executive-suite',   'floor' => '20', 'quantity' => 5,  'base_price' => 599.00],
                ],
            ],
            [
                'hotel' => [
                    'author_id'         => $vendor?->id,
                    'name'              => 'Sunset Beach Resort',
                    'slug'              => 'sunset-beach-resort',
                    'star_rating'       => 4,
                    'description'       => 'A relaxing beachfront resort perfect for families.',
                    'short_description' => 'Family-friendly 4-star beachfront resort.',
                    'address'           => '45 Corniche Road',
                    'city'              => 'Abu Dhabi',
                    'country'           => 'AE',
                    'postal_code'       => '00001',
                    'latitude'          => 24.46670,
                    'longitude'         => 54.36670,
                    'email'             => 'info@sunsetbeach.com',
                    'phone'             => '+971-2-000-0002',
                    'check_in_time'     => '14:00',
                    'check_out_time'    => '11:00',
                    'currency'          => 'USD',
                    'payment_methods'   => json_encode(['cash', 'card']),
                    'languages_spoken'  => json_encode(['en', 'ar', 'fr']),
                    'status'            => 'active',
                    'is_featured'       => false,
                    'sort_order'        => 2,
                    'base_price'        => 180.00,
                ],
                'rooms' => [
                    ['room_type_slug' => 'standard-twin-room',  'floor' => '2', 'quantity' => 20, 'base_price' => 180.00],
                    ['room_type_slug' => 'family-beach-suite',  'floor' => '1', 'quantity' => 8,  'base_price' => 450.00],
                ],
            ],
        ];

        foreach ($hotels as $entry) {
            $hotel = Hotel::firstOrCreate(
                ['slug' => $entry['hotel']['slug']],
                $entry['hotel']
            );

            foreach ($entry['rooms'] as $roomData) {
                $roomType = $createdTypes[$roomData['room_type_slug']];
                HotelRoom::firstOrCreate(
                    ['hotel_id' => $hotel->id, 'room_type_id' => $roomType->id],
                    [
                        'hotel_id'     => $hotel->id,
                        'room_type_id' => $roomType->id,
                        'floor'        => $roomData['floor'],
                        'quantity'     => $roomData['quantity'],
                        'base_price'   => $roomData['base_price'],
                        'is_active'    => true,
                    ]
                );
            }
        }
    }
}
