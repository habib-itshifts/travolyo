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

            // Room reference — string from B2B sheet (e.g. "Superior Deluxe Room")
            $table->string('room_type', 100);

            // Optional link to our DB room once matched
            $table->foreignId('hotel_room_id')->nullable()->constrained('hotel_rooms')->nullOnDelete();

            // Booking restrictions
            $table->string('release_period', 100)->nullable();        // "04 Days Prior" | "High: 06 Days Prior"
            $table->string('booking_window', 100)->nullable();        // "Open Window" | "2026-02-28"

            // Cancellation
            $table->string('cancellation_policy', 100)->nullable();   // "NRF" | "48 Hours Prior"

            // Occupancy label as received from B2B sheet
            $table->string('max_occupancy_label', 150)->nullable();   // "02 Adults +01 child"

            // Allocation
            $table->string('allocation', 100)->nullable();            // "09 Rooms" | "Subject to stop sale"

            // Blackout dates stored as simple JSON array
            $table->json('blackout_dates')->nullable();               // ["2026-12-24", "2026-12-25"]

            // Notes
            $table->text('special_remarks')->nullable();

            // Status
            $table->string('status', 20)->default('draft');           // draft | published

            $table->timestamps();

            $table->index(['hotel_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_deals');
    }
};
