<?php

namespace Modules\Hotel\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Hotel\Models\Hotel;
use Modules\Hotel\Models\HotelRoom;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        $vendor = User::where('email', 'vendor@travolyo.com')->first();

        $hotels = [
            [
                'hotel' => [
                    'author_id'         => $vendor?->id,
                    'name'              => 'Grand Palace Hotel',
                    'slug'              => 'grand-palace-hotel',
                    'star_rating'       => 5,
                    'description'       => 'A luxurious 5-star hotel in the heart of the city, offering world-class amenities and unparalleled service.',
                    'short_description' => 'Luxury 5-star city-centre hotel.',
                    'address'           => '123 King Street',
                    'city'              => 'Dubai',
                    'state'             => null,
                    'country'           => 'AE',
                    'postal_code'       => '00000',
                    'latitude'          => 25.20480,
                    'longitude'         => 55.27080,
                    'email'             => 'info@grandpalace.com',
                    'phone'             => '+971-4-000-0001',
                    'website'           => 'https://grandpalace.com',
                    'check_in_time'     => '15:00',
                    'check_out_time'    => '12:00',
                    'payment_methods'   => json_encode(['cash', 'card', 'bank_transfer']),
                    'languages_spoken'  => json_encode(['en', 'ar']),
                    'status'            => 'active',
                    'is_featured'       => true,
                    'sort_order'        => 1,
                    'base_price'        => 350.00,
                    'sale_price'        => 299.00,
                    'min_day_before_booking' => 1,
                    'min_day_stays'     => 1,
                ],
                'rooms' => [
                    [
                        'name'              => 'Deluxe King Room',
                        'slug'              => 'deluxe-king-room',
                        'room_type'         => 'double',
                        'bed_configuration' => json_encode(['king' => 1]),
                        'max_adults'        => 2,
                        'max_children'      => 1,
                        'max_occupancy'     => 3,
                        'size_sqm'          => 38.00,
                        'floor'             => '5',
                        'view_type'         => 'city',
                        'description'       => 'Spacious king room with stunning city views and premium furnishings.',
                        'currency'          => 'USD',
                        'base_price'        => 299.00,
                        'extra_adult_price' => 50.00,
                        'extra_child_price' => 25.00,
                        'quantity'          => 10,
                        'is_active'         => true,
                        'sort_order'        => 1,
                    ],
                    [
                        'name'              => 'Executive Suite',
                        'slug'              => 'executive-suite',
                        'room_type'         => 'suite',
                        'bed_configuration' => json_encode(['king' => 1, 'sofa_bed' => 1]),
                        'max_adults'        => 2,
                        'max_children'      => 2,
                        'max_occupancy'     => 4,
                        'size_sqm'          => 72.00,
                        'floor'             => '20',
                        'view_type'         => 'sea',
                        'description'       => 'Elegant suite with separate living area and panoramic sea views.',
                        'currency'          => 'USD',
                        'base_price'        => 599.00,
                        'extra_adult_price' => 80.00,
                        'extra_child_price' => 30.00,
                        'quantity'          => 5,
                        'is_active'         => true,
                        'sort_order'        => 2,
                    ],
                ],
            ],
            [
                'hotel' => [
                    'author_id'         => $vendor?->id,
                    'name'              => 'Sunset Beach Resort',
                    'slug'              => 'sunset-beach-resort',
                    'star_rating'       => 4,
                    'description'       => 'A relaxing beachfront resort perfect for families and couples, with direct beach access and multiple pools.',
                    'short_description' => 'Family-friendly 4-star beachfront resort.',
                    'address'           => '45 Corniche Road',
                    'city'              => 'Abu Dhabi',
                    'state'             => null,
                    'country'           => 'AE',
                    'postal_code'       => '00001',
                    'latitude'          => 24.46670,
                    'longitude'         => 54.36670,
                    'email'             => 'info@sunsetbeach.com',
                    'phone'             => '+971-2-000-0002',
                    'website'           => 'https://sunsetbeachresort.com',
                    'check_in_time'     => '14:00',
                    'check_out_time'    => '11:00',
                    'payment_methods'   => json_encode(['cash', 'card']),
                    'languages_spoken'  => json_encode(['en', 'ar', 'fr']),
                    'status'            => 'active',
                    'is_featured'       => false,
                    'sort_order'        => 2,
                    'base_price'        => 180.00,
                    'sale_price'        => null,
                    'min_day_before_booking' => 2,
                    'min_day_stays'     => 2,
                ],
                'rooms' => [
                    [
                        'name'              => 'Standard Twin Room',
                        'slug'              => 'standard-twin-room',
                        'room_type'         => 'twin',
                        'bed_configuration' => json_encode(['twin' => 2]),
                        'max_adults'        => 2,
                        'max_children'      => 0,
                        'max_occupancy'     => 2,
                        'size_sqm'          => 28.00,
                        'floor'             => '2',
                        'view_type'         => 'garden',
                        'description'       => 'Comfortable twin room overlooking the tropical garden.',
                        'currency'          => 'USD',
                        'base_price'        => 180.00,
                        'extra_adult_price' => 0.00,
                        'extra_child_price' => 0.00,
                        'quantity'          => 20,
                        'is_active'         => true,
                        'sort_order'        => 1,
                    ],
                    [
                        'name'              => 'Family Beach Suite',
                        'slug'              => 'family-beach-suite',
                        'room_type'         => 'family',
                        'bed_configuration' => json_encode(['king' => 1, 'twin' => 2]),
                        'max_adults'        => 2,
                        'max_children'      => 3,
                        'max_occupancy'     => 5,
                        'size_sqm'          => 85.00,
                        'floor'             => '1',
                        'view_type'         => 'sea',
                        'description'       => 'Spacious family suite steps from the beach with direct sea views.',
                        'currency'          => 'USD',
                        'base_price'        => 450.00,
                        'extra_adult_price' => 60.00,
                        'extra_child_price' => 20.00,
                        'quantity'          => 8,
                        'is_active'         => true,
                        'sort_order'        => 2,
                    ],
                ],
            ],
        ];

        foreach ($hotels as $entry) {
            $hotel = Hotel::firstOrCreate(
                ['slug' => $entry['hotel']['slug']],
                $entry['hotel']
            );

            foreach ($entry['rooms'] as $roomData) {
                HotelRoom::firstOrCreate(
                    ['hotel_id' => $hotel->id, 'slug' => $roomData['slug']],
                    array_merge($roomData, ['hotel_id' => $hotel->id])
                );
            }
        }
    }
}
