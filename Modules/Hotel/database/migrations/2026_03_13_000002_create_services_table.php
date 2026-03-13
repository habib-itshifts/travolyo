<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->string('icon', 100)->nullable();
            $table->string('category', 50)->default('general');    // transport|food|wellness|business|housekeeping|concierge
            $table->string('description', 255)->nullable();
            $table->boolean('is_chargeable')->default(false);      // free vs paid
            $table->decimal('default_price', 10, 2)->nullable();
            $table->string('price_type', 30)->nullable();          // per_person|per_night|one_time|per_request
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
