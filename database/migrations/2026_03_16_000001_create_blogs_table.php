<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->string('status', 20)->default('draft');
            $table->unsignedBigInteger('image_id')->nullable();
            $table->unsignedBigInteger('create_user')->nullable();
            $table->unsignedBigInteger('update_user')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('image_id')->references('id')->on('media_files')->nullOnDelete();
            $table->foreign('create_user')->references('id')->on('users')->nullOnDelete();
            $table->foreign('update_user')->references('id')->on('users')->nullOnDelete();
            $table->index(['status', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
