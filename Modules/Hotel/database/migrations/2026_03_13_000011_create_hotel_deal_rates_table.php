<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_deal_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_deal_id')->constrained('hotel_deals')->cascadeOnDelete();

            // Travel date window for this rate row
            $table->date('travel_date_start');
            $table->date('travel_date_end');

            // BB = Bed & Breakfast pricing (per room per night)
            $table->decimal('price_sgl_bb', 10, 2)->nullable();        // Single occupancy + breakfast
            $table->decimal('price_dbl_bb', 10, 2)->nullable();        // Double occupancy + breakfast

            // Extras
            $table->decimal('extra_bed_price', 10, 2)->nullable();     // Extra bed per night
            $table->decimal('child_price', 10, 2)->nullable();         // Child 0–11.99 yrs per night
            $table->decimal('child_breakfast', 10, 2)->nullable();     // Breakfast for child separately

            $table->timestamps();

            $table->index(['hotel_deal_id', 'travel_date_start', 'travel_date_end'], 'hdr_deal_travel_dates_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_deal_rates');
    }
};
