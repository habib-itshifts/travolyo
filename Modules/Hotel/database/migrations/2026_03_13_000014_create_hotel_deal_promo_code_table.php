<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_deal_promo_code', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_deal_id')->constrained('hotel_deals')->cascadeOnDelete();
            $table->foreignId('promo_code_id')->constrained('promo_codes')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['hotel_deal_id', 'promo_code_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_deal_promo_code');
    }
};
