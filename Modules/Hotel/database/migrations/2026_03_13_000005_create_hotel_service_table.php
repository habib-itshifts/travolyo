<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->boolean('is_available')->default(true);
            $table->decimal('price', 10, 2)->nullable();            // overrides service default_price
            $table->string('price_type', 30)->nullable();           // overrides service price_type
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->unique(['hotel_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_service');
    }
};
