<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_booking_passengers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
            $table->string('title', 20)->nullable();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->date('dob')->nullable();
            $table->string('nationality', 5)->nullable();
            $table->string('gender', 1)->nullable();
            $table->string('passport_number', 50)->nullable();
            $table->date('passport_expiry_date')->nullable();
            $table->string('contact_email', 191)->nullable();
            $table->string('contact_phone', 50)->nullable();
            $table->unsignedInteger('participants')->default(1);
            $table->date('activity_date')->nullable();
            $table->text('special_requests')->nullable();
            $table->string('payment_gateway', 50)->nullable();
            $table->timestamps();

            $table->unique('booking_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_booking_passengers');
    }
};
