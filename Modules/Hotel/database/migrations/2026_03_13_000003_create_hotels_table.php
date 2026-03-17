<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();

            // Ownership — admin or vendor
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();

            // Basic info
            $table->string('name', 191);
            $table->string('slug', 191)->unique();
            $table->unsignedTinyInteger('star_rating')->nullable();   // 1–5
            $table->text('description')->nullable();
            $table->string('short_description', 500)->nullable();

            // Location
            $table->string('address', 255);
            $table->string('city', 100);
            $table->string('state', 100)->nullable();
            $table->char('country', 2);                               // ISO 3166-1 alpha-2
            $table->string('postal_code', 20)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Contact
            $table->string('email', 191)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('website', 255)->nullable();

            // Policies
            $table->string('check_in_time', 5)->default('14:00');     // HH:MM
            $table->string('check_out_time', 5)->default('11:00');

            // JSON display-only fields (not filterable)
            $table->json('payment_methods')->nullable();              // ["cash","card","bank_transfer"]
            $table->json('languages_spoken')->nullable();             // ["en","ar","fr"]

            // Status & visibility
            $table->string('status', 20)->default('draft');           // draft|active|inactive|suspended
            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
