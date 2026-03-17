<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Document identity
            $table->string('type', 50);                        // VendorDocumentTypeEnum value
            $table->string('file_path', 500);                  // Stored file path (relative to storage/app)
            $table->string('original_name', 255);              // Original filename uploaded by vendor
            $table->string('mime_type', 100)->nullable();      // e.g. application/pdf, image/jpeg
            $table->unsignedBigInteger('file_size')->nullable(); // File size in bytes

            // Admin review
            $table->string('status', 20)->default('pending');  // VendorDocumentStatusEnum value
            $table->text('admin_note')->nullable();            // Admin feedback (e.g. reason for rejection)
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_documents');
    }
};
