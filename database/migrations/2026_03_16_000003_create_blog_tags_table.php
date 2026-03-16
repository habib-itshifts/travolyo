<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('content')->nullable();
            $table->unsignedBigInteger('create_user')->nullable();
            $table->unsignedBigInteger('update_user')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('create_user')->references('id')->on('users')->nullOnDelete();
            $table->foreign('update_user')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_tags');
    }
};
