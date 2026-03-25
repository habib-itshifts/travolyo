<?php

use Illuminate\Database\Migrations\Migration;

/**
 * Rates are now stored directly in the hotel_deals table.
 * This migration is intentionally empty — kept for ordering.
 */
return new class extends Migration
{
    public function up(): void
    {
        // No-op: rates merged into hotel_deals table.
    }

    public function down(): void
    {
        // No-op
    }
};
