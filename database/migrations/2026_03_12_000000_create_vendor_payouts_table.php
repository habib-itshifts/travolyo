<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('users')->cascadeOnDelete();

            // Period covered by this payout
            $table->date('period_start');
            $table->date('period_end');

            // Financials
            $table->string('currency', 3)->default('USD');
            $table->decimal('total_bookings_amount', 14, 2)->default(0); // sum of booking totals in period
            $table->decimal('commission_deducted', 12, 2)->default(0);   // total commission kept by platform
            $table->decimal('fees_deducted', 12, 2)->default(0);         // vendor service fees deducted
            $table->decimal('refunds_deducted', 12, 2)->default(0);      // refunds charged back to vendor
            $table->decimal('payout_amount', 12, 2)->default(0);         // net amount paid to vendor

            // Status
            $table->string('status', 30)->default('pending');  // pending | processing | completed | failed
            $table->string('payment_method', 50)->nullable();  // bank_transfer | wallet | cheque | ...
            $table->string('transaction_reference', 255)->nullable();

            // Audit
            $table->text('notes')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete(); // admin who approved
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->index(['vendor_id', 'status']);
            $table->index(['period_start', 'period_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_payouts');
    }
};
