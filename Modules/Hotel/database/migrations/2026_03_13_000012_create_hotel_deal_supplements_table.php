<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_deal_supplements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_deal_id')->constrained('hotel_deals')->cascadeOnDelete();

            // Event name e.g. "Eid Al Fitr", "Arab Health", "Eid Al Adha"
            $table->string('event_name', 100);

            // Date range this supplement applies to
            $table->date('date_start');
            $table->date('date_end');

            // Supplement amount per room per night (PRPN)
            $table->decimal('amount', 10, 2);

            $table->timestamps();

            $table->index(['hotel_deal_id', 'date_start', 'date_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_deal_supplements');
    }
};
