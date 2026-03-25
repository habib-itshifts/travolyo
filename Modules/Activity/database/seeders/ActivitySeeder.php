<?php

namespace Modules\Activity\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Activity\Models\Activity;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $author = User::query()
            ->where('email', 'admin@travolyo.com')
            ->orWhere('email', 'vendor@travolyo.com')
            ->first();

        $activities = [
            [
                'slug' => 'dubai-desert-safari-experience',
                'title' => 'Dubai Desert Safari Experience',
                'category' => 'Desert',
                'city' => 'Dubai',
                'country' => 'UAE',
                'address' => 'Al Aweer Desert Meeting Point, Dubai',
                'price_per_person' => 250.00,
                'currency' => 'AED',
                'max_participants' => 50,
                'duration' => '6 Hours',
                'instant_confirmation' => true,
                'is_active' => true,
                'email_flyer_enabled' => true,
                'description' => 'Enjoy an evening Dubai desert safari with dune bashing, sunset views, camel rides, live entertainment, and a BBQ dinner in a traditional desert camp.',
                'extra_information' => [
                    'Hotel pickup and drop-off included',
                    'Dune bashing in a 4x4 vehicle',
                    'Camel ride and sandboarding experience',
                    'BBQ dinner with live cultural shows',
                ],
            ],
            [
                'slug' => 'dubai-marina-dhow-cruise-dinner',
                'title' => 'Dubai Marina Dhow Cruise Dinner',
                'category' => 'Cruise',
                'city' => 'Dubai',
                'country' => 'UAE',
                'address' => 'Dubai Marina Harbour, Dubai',
                'price_per_person' => 180.00,
                'currency' => 'AED',
                'max_participants' => 80,
                'duration' => '2 Hours',
                'instant_confirmation' => true,
                'is_active' => true,
                'email_flyer_enabled' => true,
                'description' => 'Sail through Dubai Marina on a traditional dhow cruise with buffet dinner, skyline views, and live entertainment.',
                'extra_information' => [
                    'Welcome drinks on arrival',
                    'International buffet dinner included',
                    'Live Tanoura dance performance',
                    'Reserved seating with marina views',
                ],
            ],
            [
                'slug' => 'burj-khalifa-at-the-top-ticket',
                'title' => 'Burj Khalifa At The Top Ticket',
                'category' => 'Sightseeing',
                'city' => 'Dubai',
                'country' => 'UAE',
                'address' => 'Burj Khalifa, Downtown Dubai',
                'price_per_person' => 199.00,
                'currency' => 'AED',
                'max_participants' => 30,
                'duration' => '1.5 Hours',
                'instant_confirmation' => true,
                'is_active' => true,
                'email_flyer_enabled' => true,
                'description' => 'Visit the iconic Burj Khalifa observation deck and enjoy panoramic views of Downtown Dubai from one of the tallest towers in the world.',
                'extra_information' => [
                    'Timed entry to observation deck',
                    'Access to high-speed elevators',
                    'Best for sunset and evening city views',
                    'E-ticket confirmation by email',
                ],
            ],
            [
                'slug' => 'abu-dhabi-city-tour',
                'title' => 'Abu Dhabi City Tour',
                'category' => 'City Tour',
                'city' => 'Abu Dhabi',
                'country' => 'UAE',
                'address' => 'Abu Dhabi Corniche Pickup Point, Abu Dhabi',
                'price_per_person' => 220.00,
                'currency' => 'AED',
                'max_participants' => 40,
                'duration' => '8 Hours',
                'instant_confirmation' => true,
                'is_active' => true,
                'email_flyer_enabled' => true,
                'description' => 'Discover the capital city with a guided Abu Dhabi tour covering the Corniche, Emirates Palace, heritage attractions, and major landmarks.',
                'extra_information' => [
                    'Shared transport with hotel pickup',
                    'Professional English-speaking guide',
                    'Photo stop at Emirates Palace and Corniche',
                    'Ideal full-day introduction to Abu Dhabi',
                ],
            ],
            [
                'slug' => 'sheikh-zayed-grand-mosque-tour',
                'title' => 'Sheikh Zayed Grand Mosque Tour',
                'category' => 'Cultural',
                'city' => 'Abu Dhabi',
                'country' => 'UAE',
                'address' => 'Sheikh Zayed Grand Mosque Centre, Abu Dhabi',
                'price_per_person' => 150.00,
                'currency' => 'AED',
                'max_participants' => 35,
                'duration' => '3 Hours',
                'instant_confirmation' => true,
                'is_active' => true,
                'email_flyer_enabled' => true,
                'description' => 'Explore one of the world\'s most stunning mosques with a guided cultural visit highlighting its architecture, chandeliers, and reflective pools.',
                'extra_information' => [
                    'Guided entry to Sheikh Zayed Grand Mosque',
                    'Cultural dress guidance before entry',
                    'Plenty of time for photos and exploration',
                    'Morning and afternoon slot availability',
                ],
            ],
            [
                'slug' => 'ferrari-world-abu-dhabi-pass',
                'title' => 'Ferrari World Abu Dhabi Pass',
                'category' => 'Family',
                'city' => 'Abu Dhabi',
                'country' => 'UAE',
                'address' => 'Ferrari World, Yas Island, Abu Dhabi',
                'price_per_person' => 325.00,
                'currency' => 'AED',
                'max_participants' => 60,
                'duration' => 'Full Day',
                'instant_confirmation' => true,
                'is_active' => true,
                'email_flyer_enabled' => true,
                'description' => 'Enjoy a full day at Ferrari World Abu Dhabi with access to thrilling rides, family attractions, and entertainment on Yas Island.',
                'extra_information' => [
                    'General admission pass included',
                    'Access to family rides and thrill attractions',
                    'Located on Yas Island',
                    'Instant e-ticket confirmation',
                ],
            ],
        ];

        foreach ($activities as $activityData) {
            Activity::updateOrCreate(
                ['slug' => $activityData['slug']],
                array_merge($activityData, [
                    'gallery' => null,
                    'image_id' => null,
                    'author_id' => $author?->id,
                    'status' => 'publish',
                    'create_user' => $author?->id,
                    'update_user' => $author?->id,
                ])
            );
        }
    }
}
