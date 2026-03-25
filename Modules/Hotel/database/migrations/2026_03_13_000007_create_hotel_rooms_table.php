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
            $table->foreignId('room_type_id')->constrained('room_types')->cascadeOnDelete();

            // Media (hotel-specific photos for this room)
            $table->unsignedBigInteger('image_id')->nullable();
            $table->text('gallery')->nullable();

            // Hotel-specific overrides
            $table->string('floor', 20)->nullable();
            $table->unsignedSmallInteger('quantity')->default(1);     // how many of this room type exist

            // Pricing (fallback when no deal exists)
            $table->decimal('price_sgl_bb', 10, 2)->nullable();       // Single occupancy + breakfast
            $table->decimal('price_dbl_bb', 10, 2)->nullable();       // Double occupancy + breakfast
            $table->decimal('extra_bed_price', 10, 2)->nullable();    // Extra bed per night
            $table->decimal('child_price', 10, 2)->nullable();        // Child per night
            $table->decimal('child_breakfast', 10, 2)->nullable();    // Breakfast for child

            // Availability
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();

            $table->unique(['hotel_id', 'room_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_rooms');
    }
};
