<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spaces', function (Blueprint $table) {
            $table->id();

            // Ownership — admin or vendor
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();

            // Basic info
            $table->string('name', 191);
            $table->string('slug', 191)->unique();
            $table->text('description')->nullable();
            $table->string('short_description', 500)->nullable();
            $table->string('type', 50)->default('apartment'); // apartment|room|studio|villa|house

            // Capacity
            $table->unsignedTinyInteger('max_guests')->default(1);
            $table->unsignedTinyInteger('bedrooms')->default(1);
            $table->unsignedTinyInteger('bathrooms')->default(1);
            $table->unsignedTinyInteger('beds')->default(1);

            // Media IDs (linked to media_files table)
            $table->unsignedBigInteger('image_id')->nullable();
            $table->unsignedBigInteger('banner_image_id')->nullable();
            $table->text('gallery')->nullable();

            // Media URLs (direct paths)
            $table->string('featured_image_url', 2048)->nullable();
            $table->string('banner_image_url', 2048)->nullable();
            $table->json('gallery_urls')->nullable();
            $table->string('video_url', 2048)->nullable();

            // Location
            $table->string('address', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->char('country', 2)->nullable();               // ISO 3166-1 alpha-2
            $table->string('postal_code', 20)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Contact
            $table->string('email', 191)->nullable();
            $table->string('phone', 50)->nullable();

            // Policies
            $table->string('check_in_time', 5)->default('14:00');  // HH:MM
            $table->string('check_out_time', 5)->default('11:00');
            $table->unsignedInteger('min_day_before_booking')->nullable();
            $table->unsignedInteger('min_stay_nights')->default(1);
            $table->unsignedInteger('max_stay_nights')->nullable();
            $table->json('house_rules')->nullable();               // [{"title":"...","content":"..."}]
            $table->text('cancellation_policy')->nullable();

            // Pricing
            $table->decimal('price_per_night', 12, 2)->nullable();
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->decimal('cleaning_fee', 12, 2)->nullable();
            $table->decimal('service_fee', 12, 2)->nullable();
            $table->json('extra_prices')->nullable();              // [{name, price, type, per_person}]
            $table->string('currency', 3)->default('USD');

            // Status & visibility
            $table->string('status', 20)->default('draft');        // draft|active|inactive|suspended
            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index('author_id');
            $table->index('status');
            $table->index('city');
            $table->index('country');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spaces');
    }
};
