<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->string('scraping_url', 500)->nullable()->after('slug');
            $table->index(['author_id', 'scraping_url'], 'hotels_author_scraping_url_index');
        });
    }

    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropIndex('hotels_author_scraping_url_index');
            $table->dropColumn('scraping_url');
        });
    }
};
