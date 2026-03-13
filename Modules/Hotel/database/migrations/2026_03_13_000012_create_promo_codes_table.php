<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();

            $table->string('code', 100)->unique();                    // "TAEXCLUO226", "SVHPROMO-1902"
            $table->string('label', 150)->nullable();                 // "Flash Sale", "Offline Deal", "Contracted Rates"
            $table->string('type', 30)->default('online');            // online | offline | contracted | flash_sale
            $table->string('description', 255)->nullable();

            // Optional discount — some promos are just labels, others give a % or fixed discount
            $table->string('discount_type', 20)->nullable();          // percent | fixed | null
            $table->decimal('discount_value', 10, 2)->nullable();     // e.g. 10.00 for 10% or AED 50

            // Validity
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
    }
};
