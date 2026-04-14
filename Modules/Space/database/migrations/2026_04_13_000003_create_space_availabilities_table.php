<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('space_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('space_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->boolean('is_available')->default(true);
            $table->decimal('price_override', 12, 2)->nullable(); // custom price for this date
            $table->unsignedTinyInteger('min_stay')->nullable();   // override min stay for this date
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->unique(['space_id', 'date']);
            $table->index(['space_id', 'date', 'is_available']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('space_availabilities');
    }
};
