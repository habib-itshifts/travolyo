<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            if (! Schema::hasColumn('blogs', 'author_id')) {
                $table->unsignedBigInteger('author_id')->nullable()->after('image_id');
                $table->foreign('author_id')->references('id')->on('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('blogs', 'cat_id')) {
                $table->unsignedBigInteger('cat_id')->nullable()->after('author_id');
                $table->foreign('cat_id')->references('id')->on('blog_categories')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            if (Schema::hasColumn('blogs', 'cat_id')) {
                $table->dropForeign(['cat_id']);
                $table->dropColumn('cat_id');
            }

            if (Schema::hasColumn('blogs', 'author_id')) {
                $table->dropForeign(['author_id']);
                $table->dropColumn('author_id');
            }
        });
    }
};
