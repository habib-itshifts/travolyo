<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('content')->nullable();
            $table->string('slug')->unique();
            $table->string('status', 20)->default('publish');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('create_user')->nullable();
            $table->unsignedBigInteger('update_user')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('parent_id')->references('id')->on('blog_categories')->nullOnDelete();
            $table->foreign('create_user')->references('id')->on('users')->nullOnDelete();
            $table->foreign('update_user')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_categories');
    }
};
