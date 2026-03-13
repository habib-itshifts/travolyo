<?php

namespace Tests\Feature\Admin;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Admin\Models\MediaFile;
use Modules\Hotel\Models\Hotel;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class HotelStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_store_hotel_with_media_ids_and_synced_paths(): void
    {
        $admin = User::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'Tester',
            'name' => 'Admin Tester',
            'user_type' => UserType::Admin,
        ]);

        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->assignRole($role);

        $featured = MediaFile::create([
            'file_name' => 'featured.jpg',
            'file_path' => 'hotel-images/test-hotel-images/featured.jpg',
            'file_extension' => 'jpg',
            'file_type' => 'image/jpeg',
            'file_size' => 1024,
            'folder_path' => 'hotel-images/test-hotel-images',
        ]);

        $banner = MediaFile::create([
            'file_name' => 'banner.jpg',
            'file_path' => 'hotel-images/test-hotel-images/banner.jpg',
            'file_extension' => 'jpg',
            'file_type' => 'image/jpeg',
            'file_size' => 1024,
            'folder_path' => 'hotel-images/test-hotel-images',
        ]);

        $galleryOne = MediaFile::create([
            'file_name' => 'gallery-1.jpg',
            'file_path' => 'hotel-images/test-hotel-images/gallery-1.jpg',
            'file_extension' => 'jpg',
            'file_type' => 'image/jpeg',
            'file_size' => 1024,
            'folder_path' => 'hotel-images/test-hotel-images',
        ]);

        $galleryTwo = MediaFile::create([
            'file_name' => 'gallery-2.jpg',
            'file_path' => 'hotel-images/test-hotel-images/gallery-2.jpg',
            'file_extension' => 'jpg',
            'file_type' => 'image/jpeg',
            'file_size' => 1024,
            'folder_path' => 'hotel-images/test-hotel-images',
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.hotels.store'), [
                'name' => 'Test Hotel',
                'slug' => 'test-hotel',
                'address' => '123 Test Street',
                'city' => 'Dubai',
                'country' => 'AE',
                'status' => 'active',
                'image_id' => $featured->id,
                'banner_image_id' => $banner->id,
                'gallery' => $galleryOne->id . ',' . $galleryTwo->id,
                'video_url' => 'https://www.youtube.com/watch?v=test123',
                'base_price' => 250,
                'sale_price' => 220,
                'policies' => [
                    ['title' => 'Check-in', 'content' => 'After 2 PM'],
                ],
                'nearby_places' => [
                    ['name' => 'Airport', 'content' => 'Main airport', 'value' => 5, 'type' => 'km'],
                ],
                'extra_prices' => [
                    ['name' => 'Breakfast', 'price' => 20, 'type' => 'one_time', 'per_person' => 1],
                ],
            ]);

        $response->assertRedirect(route('admin.hotels.index'));

        $hotel = Hotel::where('slug', 'test-hotel')->first();

        $this->assertNotNull($hotel);
        $this->assertSame($featured->id, $hotel->image_id);
        $this->assertSame($banner->id, $hotel->banner_image_id);
        $this->assertSame($galleryOne->id . ',' . $galleryTwo->id, $hotel->gallery);
        $this->assertSame('uploads/' . $featured->file_path, $hotel->featured_image_url);
        $this->assertSame('uploads/' . $banner->file_path, $hotel->banner_image_url);
        $this->assertSame([
            'uploads/' . $galleryOne->file_path,
            'uploads/' . $galleryTwo->file_path,
        ], $hotel->gallery_urls);
    }
}
