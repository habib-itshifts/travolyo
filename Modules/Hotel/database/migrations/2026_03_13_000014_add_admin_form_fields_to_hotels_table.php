<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->string('featured_image_url', 2048)->nullable()->after('short_description');
            $table->string('banner_image_url', 2048)->nullable()->after('featured_image_url');
            $table->json('gallery_urls')->nullable()->after('banner_image_url');
            $table->string('video_url', 2048)->nullable()->after('gallery_urls');

            $table->decimal('base_price', 12, 2)->nullable()->after('check_out_time');
            $table->decimal('sale_price', 12, 2)->nullable()->after('base_price');
            $table->unsignedInteger('min_day_before_booking')->nullable()->after('sale_price');
            $table->unsignedInteger('min_day_stays')->nullable()->after('min_day_before_booking');

            $table->json('policies')->nullable()->after('min_day_stays');
            $table->json('nearby_places')->nullable()->after('policies');
            $table->json('extra_prices')->nullable()->after('nearby_places');
            $table->string('related_hotel_ids', 255)->nullable()->after('extra_prices');
        });
    }

    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn([
                'featured_image_url',
                'banner_image_url',
                'gallery_urls',
                'video_url',
                'base_price',
                'sale_price',
                'min_day_before_booking',
                'min_day_stays',
                'policies',
                'nearby_places',
                'extra_prices',
                'related_hotel_ids',
            ]);
        });
    }
};
