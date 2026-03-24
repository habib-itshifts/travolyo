<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('name', 191);                              // e.g. "Deluxe King", "Standard Twin"
            $table->string('slug', 191)->unique();

            // Media
            $table->unsignedBigInteger('image_id')->nullable();

            // Capacity & bed layout
            $table->json('bed_configuration')->nullable();            // {"king":1} or {"twin":2,"sofa_bed":1}
            $table->unsignedTinyInteger('max_adults')->default(2);
            $table->unsignedTinyInteger('max_children')->default(0);
            $table->unsignedTinyInteger('max_occupancy')->default(2);

            // Physical
            $table->decimal('size_sqm', 8, 2)->nullable();
            $table->string('view_type', 50)->nullable();              // sea|garden|pool|city|mountain

            // Content
            $table->text('description')->nullable();

            // Extra pricing (inherent to room type)
            $table->decimal('extra_adult_price', 10, 2)->default(0);
            $table->decimal('extra_child_price', 10, 2)->default(0);

            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};
