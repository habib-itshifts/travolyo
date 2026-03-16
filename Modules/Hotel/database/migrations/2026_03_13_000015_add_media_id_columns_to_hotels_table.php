<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->unsignedBigInteger('image_id')->nullable()->after('short_description');
            $table->unsignedBigInteger('banner_image_id')->nullable()->after('image_id');
            $table->text('gallery')->nullable()->after('banner_image_id');
        });
    }

    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn(['image_id', 'banner_image_id', 'gallery']);
        });
    }
};
