<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();          // e.g. TRV-2026-ABC123
            $table->string('object_model', 50);            // flight | hotel | ...
            $table->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete();

            // Status
            $table->string('status', 30)->default('draft');

            // Pricing
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('pay_now', 12, 2)->default(0);
            $table->decimal('paid', 12, 2)->default(0);
            $table->string('currency', 3)->default('USD');

            // Contact
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('phone', 50)->nullable();

            // Notes
            $table->text('customer_notes')->nullable();

            $table->timestamps();
        });

        Schema::create('booking_meta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->string('name', 191);
            $table->longText('val')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_meta');
        Schema::dropIfExists('bookings');
    }
};
