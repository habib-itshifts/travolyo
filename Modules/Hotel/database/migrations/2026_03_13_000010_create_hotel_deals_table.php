<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_deals', function (Blueprint $table) {
            $table->id();

            // Hotel reference (required)
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();

            // Room type reference
            $table->foreignId('room_type_id')->constrained('room_types')->cascadeOnDelete();

            // Booking restrictions
            $table->unsignedSmallInteger('release_period')->nullable();  // days
            $table->string('release_type', 100)->nullable();             // free-text label
            $table->date('booking_window')->nullable();                  // date

            // Cancellation
            $table->string('cancellation_policy', 100)->nullable();      // kept as-is (string)

            // Blackout dates stored as simple JSON array
            $table->json('blackout_dates')->nullable();                  // ["2026-12-24","2026-12-25"]

            // Notes
            $table->text('special_remarks')->nullable();

            // Rates (single row — no separate table)
            $table->date('travel_date_start')->nullable();
            $table->date('travel_date_end')->nullable();
            $table->decimal('price_sgl_bb', 10, 2)->nullable();          // Single occupancy + breakfast
            $table->decimal('price_dbl_bb', 10, 2)->nullable();          // Double occupancy + breakfast
            $table->decimal('extra_bed_price', 10, 2)->nullable();       // Extra bed per night
            $table->decimal('child_price', 10, 2)->nullable();           // Child 0–11.99 yrs per night
            $table->decimal('child_breakfast', 10, 2)->nullable();       // Breakfast for child separately

            // Status
            $table->string('status', 20)->default('draft');              // draft | published

            $table->timestamps();

            $table->index(['hotel_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_deals');
    }
};
