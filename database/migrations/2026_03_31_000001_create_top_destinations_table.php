<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('top_destinations', function (Blueprint $table) {
            $table->id();
            $table->string('city');
            $table->string('country');
            $table->string('country_code', 10)->nullable();
            $table->string('location', 20)->nullable();
            $table->string('image_path');
            $table->string('image_alt')->nullable();
            $table->string('accommodations_label', 120);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('top_destinations');
    }
};
