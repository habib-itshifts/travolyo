<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('external_hyperguest_hotels', function (Blueprint $table) {
            $table->id();

            // Hyperguest property ID (unique identifier from their API)
            $table->unsignedBigInteger('property_id')->unique();

            // Basic info
            $table->string('name', 191);
            $table->string('slug', 191)->unique();
            $table->unsignedTinyInteger('star_rating')->nullable();
            $table->text('description')->nullable();
            $table->string('short_description', 500)->nullable();

            // Media URLs
            $table->string('featured_image_url', 2048)->nullable();
            $table->json('gallery_urls')->nullable();

            // Location
            $table->string('address', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->char('country', 2)->nullable();                    // ISO 3166-1 alpha-2
            $table->string('postal_code', 20)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Policies
            $table->string('check_in_time', 5)->nullable();
            $table->string('check_out_time', 5)->nullable();

            // Pricing
            $table->decimal('base_price', 12, 2)->nullable();
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->string('currency', 3)->default('USD');

            // Status
            $table->string('status', 20)->default('active');
            $table->boolean('is_featured')->default(false);

            // Raw API data cache
            $table->json('external_data')->nullable();
            $table->timestamp('synced_at')->nullable();

            $table->timestamps();

            // Search indexes
            $table->index('city');
            $table->index('country');
            $table->index('star_rating');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('external_hyperguest_hotels');
    }
};
