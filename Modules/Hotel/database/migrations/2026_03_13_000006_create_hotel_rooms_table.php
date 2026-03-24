<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();

            // Identity
            $table->string('name', 191);                              // e.g. "Deluxe King Room"
            $table->string('slug', 191);
            $table->string('room_type', 50);                          // single|double|twin|suite|family|studio|penthouse

            // Media
            $table->unsignedBigInteger('image_id')->nullable();
            $table->text('gallery')->nullable();

            // Bed & capacity
            $table->json('bed_configuration');                        // {"king":1} or {"twin":2,"sofa_bed":1}
            $table->unsignedTinyInteger('max_adults')->default(2);
            $table->unsignedTinyInteger('max_children')->default(0);
            $table->unsignedTinyInteger('max_occupancy')->default(2);

            // Physical
            $table->decimal('size_sqm', 8, 2)->nullable();
            $table->string('floor', 20)->nullable();
            $table->string('view_type', 50)->nullable();              // sea|garden|pool|city|mountain

            // Content
            $table->text('description')->nullable();
            $table->string('currency', 3)->default('USD');

            // Pricing
            $table->decimal('base_price', 10, 2);                    // per night
            $table->decimal('extra_adult_price', 10, 2)->default(0);
            $table->decimal('extra_child_price', 10, 2)->default(0);

            // Availability
            $table->unsignedSmallInteger('quantity')->default(1);    // how many of this room type exist
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();

            $table->unique(['hotel_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_rooms');
    }
};
