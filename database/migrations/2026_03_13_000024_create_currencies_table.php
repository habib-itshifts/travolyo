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
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('label', 10);
            $table->string('symbol', 20);
            $table->string('flag', 5)->default('us');
            $table->string('name', 100);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        DB::table('currencies')->insert([
            [
                'code' => 'USD',
                'label' => 'USD',
                'symbol' => '$',
                'flag' => 'us',
                'name' => 'US Dollar',
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'GBP',
                'label' => 'GBP',
                'symbol' => 'GBP',
                'flag' => 'gb',
                'name' => 'British Pound',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'EUR',
                'label' => 'EUR',
                'symbol' => 'EUR',
                'flag' => 'eu',
                'name' => 'Euro',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'AED',
                'label' => 'AED',
                'symbol' => 'AED',
                'flag' => 'ae',
                'name' => 'UAE Dirham',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 3,
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
