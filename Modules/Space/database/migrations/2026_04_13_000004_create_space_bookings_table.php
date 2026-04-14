<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('space_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('space_id')->constrained()->cascadeOnDelete();

            // Stay details
            $table->date('check_in');
            $table->date('check_out');
            $table->unsignedSmallInteger('nights');
            $table->unsignedTinyInteger('guests')->default(1);

            // Pricing snapshot at time of booking
            $table->decimal('price_per_night', 12, 2);
            $table->decimal('cleaning_fee', 12, 2)->default(0);
            $table->decimal('service_fee', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2);
            $table->string('currency', 3)->default('USD');

            // Extras
            $table->json('extra_services')->nullable();
            $table->text('special_requests')->nullable();

            // Status
            $table->string('status', 20)->default('pending'); // pending|confirmed|checked_in|checked_out|cancelled

            $table->timestamps();

            $table->index(['space_id', 'check_in', 'check_out']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('space_bookings');
    }
};
