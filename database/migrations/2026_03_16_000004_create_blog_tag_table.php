<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_tag', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('blog_id');
            $table->unsignedBigInteger('tag_id');
            $table->unsignedBigInteger('create_user')->nullable();
            $table->unsignedBigInteger('update_user')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('blog_id')->references('id')->on('blogs')->cascadeOnDelete();
            $table->foreign('tag_id')->references('id')->on('blog_tags')->cascadeOnDelete();
            $table->foreign('create_user')->references('id')->on('users')->nullOnDelete();
            $table->foreign('update_user')->references('id')->on('users')->nullOnDelete();
            $table->unique(['blog_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_tag');
    }
};
