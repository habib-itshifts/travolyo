<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('code', 10)->index();
            $table->string('symbol', 25);
            $table->string('format', 50);
            $table->string('exchange_rate');
            $table->boolean('active')->default(false);
            $table->timestamps();
        });

        DB::table('currencies')->insert([
            [
                'name' => 'US Dollar',
                'code' => 'USD',
                'symbol' => '$',
                'format' => '$1,0.00',
                'exchange_rate' => '1.00000000',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'British Pound',
                'code' => 'GBP',
                'symbol' => '£',
                'format' => '£1,0.00',
                'exchange_rate' => '0.79000000',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
                'format' => '1.0,00€',
                'exchange_rate' => '0.92000000',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'UAE Dirham',
                'code' => 'AED',
                'symbol' => 'د.إ',
                'format' => '1,0.00 د.إ',
                'exchange_rate' => '3.67000000',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
