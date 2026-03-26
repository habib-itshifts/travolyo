<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('hotel_room_id')->constrained('hotel_rooms')->cascadeOnDelete();

            // Stay dates
            $table->date('check_in');
            $table->date('check_out');
            $table->unsignedTinyInteger('nights');

            // Guests
            $table->unsignedTinyInteger('adults')->default(1);
            $table->unsignedTinyInteger('children')->default(0);

            // Price snapshot at time of booking
            $table->decimal('unit_price', 10, 2);                   // price per night
            $table->decimal('total_price', 10, 2);                  // nights * unit_price + extras

            // Extras
            $table->json('extra_services')->nullable();              // add-ons chosen at checkout
            $table->text('special_requests')->nullable();

            // Deal reference — which deal was applied at booking time (null = standard room-type price)
            $table->foreignId('hotel_deal_id')->nullable()->constrained('hotel_deals')->nullOnDelete();

            // Status
            $table->string('status', 30)->default('pending');       // pending|confirmed|checked_in|checked_out|cancelled

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_rooms');
    }
};
