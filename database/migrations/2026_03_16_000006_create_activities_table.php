<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('address')->nullable();
            $table->decimal('price_per_person', 12, 2)->nullable();
            $table->string('currency', 10)->nullable();
            $table->unsignedInteger('max_participants')->nullable();
            $table->string('duration')->nullable();
            $table->boolean('instant_confirmation')->default(true);
            $table->boolean('is_active')->default(true);
            $table->boolean('email_flyer_enabled')->default(true);
            $table->longText('description')->nullable();
            $table->json('extra_information')->nullable();
            $table->text('gallery')->nullable();
            $table->unsignedBigInteger('image_id')->nullable();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->string('status', 20)->default('publish');
            $table->unsignedBigInteger('create_user')->nullable();
            $table->unsignedBigInteger('update_user')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
