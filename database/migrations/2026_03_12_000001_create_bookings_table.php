<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();          // e.g. TRV-2026-ABC123
            $table->string('object_model', 50);            // flight | hotel | tour | ...
            $table->unsignedBigInteger('object_id')->nullable();  // FK to the booked item

            // Ownership
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();   // who created the booking
            $table->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete(); // the traveller
            $table->foreignId('vendor_id')->nullable()->constrained('users')->nullOnDelete();   // service provider

            // Status
            $table->string('status', 30)->default('draft');       // draft|pending|confirmed|cancelled|completed
            $table->boolean('is_paid')->default(false);

            // Dates
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->unsignedSmallInteger('total_guests')->nullable();

            // Pricing breakdown
            $table->string('currency', 3)->default('USD');
            $table->decimal('total_before_discount', 12, 2)->default(0);
            $table->decimal('coupon_amount', 12, 2)->default(0);
            $table->decimal('total_before_fees', 12, 2)->default(0);
            $table->decimal('buyer_fees', 12, 2)->default(0);         // fees charged to customer on top
            $table->decimal('total', 12, 2)->default(0);              // final amount customer owes
            $table->decimal('pay_now', 12, 2)->default(0);            // deposit / pay-now portion
            $table->decimal('paid', 12, 2)->default(0);               // amount actually received from customer

            // Commission (platform keeps this)
            $table->string('commission_type', 30)->nullable();         // percent | fixed
            $table->decimal('commission', 10, 2)->nullable();          // rate or fixed value
            $table->decimal('commission_amount', 10, 2)->default(0);   // calculated commission kept by platform

            // Vendor payout
            $table->decimal('vendor_service_fee', 10, 2)->default(0);  // extra fee charged to vendor
            $table->decimal('vendor_amount', 12, 2)->default(0);        // net amount owed to vendor (total - commission_amount - vendor_service_fee)
            $table->foreignId('vendor_payout_id')->nullable()->constrained('vendor_payouts')->nullOnDelete(); // batch payout reference
            $table->timestamp('vendor_paid_at')->nullable();            // when vendor payment was released

            // Refund
            $table->decimal('refund_amount', 12, 2)->default(0);
            $table->string('refund_status', 30)->default('none');       // none | pending | partial | full
            $table->timestamp('refunded_at')->nullable();

            // Contact
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('zip_code', 20)->nullable();
            $table->string('country', 100)->nullable();

            // Origin tracking
            $table->string('source', 50)->nullable();    // local | hyperguest | duffel | b2b | ...
            $table->string('platform', 20)->nullable();  // web | mobile

            // Notes
            $table->text('customer_notes')->nullable();

            // Soft delete + audit
            $table->foreignId('create_user')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('update_user')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['object_model', 'object_id']);
            $table->index('vendor_id');
            $table->index('status');
        });

        Schema::create('booking_meta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->string('name', 191);
            $table->longText('val')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_meta');
        Schema::dropIfExists('bookings');
    }
};
