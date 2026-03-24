<?php

use App\Enums\UserType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Primary key — auto-incrementing unsigned big integer

            // ─── Identity ────────────────────────────────────────────────────
            $table->string('first_name')->nullable();                          // User's legal first name
            $table->string('last_name')->nullable();                           // User's legal last name
            $table->string('name');                           // concatenated full name for display purposes (e.g. "John Doe")
            $table->string('username')->unique()->nullable();      // Optional unique public handle (e.g. @john_doe)
            $table->string('email')->unique();                     // Login email — must be unique across all users
            $table->timestamp('email_verified_at')->nullable();    // Set when user clicks the verification link; null = unverified
            $table->string('password');                            // Bcrypt-hashed password
            $table->string('gender', 20)->nullable();              // male | female | other — kept as string for flexibility
            $table->date('birthday')->nullable();                  // Date of birth — used for age validation and personalisation
            $table->string('avatar')->nullable();                  // Path/URL to profile picture (fallback; prefer spatie/medialibrary)
            $table->string('nationality')->nullable();             // User's nationality (e.g. Pakistani, American)

            // ─── User Type ───────────────────────────────────────────────────
            $table->string('user_type')->default(UserType::Customer->value); // Discriminator: admin | vendor | customer

            // ─── Contact ─────────────────────────────────────────────────────
            $table->string('phone')->nullable();                   // Primary phone number (digits only, no country code)
            $table->string('phone_country_code', 10)->nullable();  // Dial code prefix, e.g. +92, +1
            $table->string('whatsapp_number')->nullable();         // WhatsApp number if different from phone

            // ─── Address ─────────────────────────────────────────────────────
            $table->string('address_line_1')->nullable();          // Street address, P.O. box, company name
            $table->string('address_line_2')->nullable();          // Apartment, suite, unit, building, floor, etc.
            $table->string('city')->nullable();                    // City or district
            $table->string('state')->nullable();                   // State, province, or region
            $table->string('country')->nullable();                 // Country name or ISO 3166-1 alpha-2 code
            $table->string('zip_code')->nullable();                // Postal / ZIP code

            // ─── Business / Vendor ───────────────────────────────────────────
            $table->string('business_name')->nullable();           // Registered business or brand name (vendors)
            $table->string('vendor_commission_type')->nullable();  // Commission structure: 'percentage' | 'fixed'
            $table->decimal('vendor_commission_amount', 10, 2)->default(0); // Commission rate or flat fee amount
            $table->string('vendor_status')->nullable();           // null | pending | approved | docs_submitted | verified | rejected
            $table->timestamp('vendor_verified_at')->nullable();   // Timestamp of when vendor was fully verified
            $table->string('tax_number')->nullable();              // VAT / GST / NTN registration number

            // ─── Financial ───────────────────────────────────────────────────
            $table->decimal('credit_balance', 15, 2)->default(0); // Wallet credit balance (in base currency)
            $table->string('currency_preference', 3)->default('USD'); // ISO 4217 currency code preferred by the user
            $table->string('stripe_customer_id')->nullable();      // Stripe customer object ID (cus_xxx) for payments

            // ─── Social Auth ─────────────────────────────────────────────────
            $table->string('social_provider')->nullable();         // OAuth provider name: google | facebook | github etc.
            $table->string('social_provider_id')->nullable();      // Unique ID returned by the OAuth provider

            // ─── Two-Factor Authentication ───────────────────────────────────
            $table->text('two_factor_secret')->nullable();         // Encrypted TOTP secret key (e.g. Google Authenticator)
            $table->text('two_factor_recovery_codes')->nullable(); // JSON array of one-time recovery codes
            $table->timestamp('two_factor_confirmed_at')->nullable(); // Timestamp when user first confirmed 2FA setup

            // ─── Preferences ─────────────────────────────────────────────────
            $table->string('preferred_language', 10)->default('en'); // BCP 47 language tag, e.g. en, ar, fr
            $table->string('timezone')->nullable();                // IANA timezone, e.g. Asia/Karachi, America/New_York

            // ─── Status & Audit ──────────────────────────────────────────────
            $table->boolean('is_active')->default(true);           // False = account disabled (cannot log in)
            $table->boolean('is_published')->default(false);       // Vendor profile visible to the public
            $table->boolean('is_verified')->default(false);        // Manual identity/document verification by admin
            $table->timestamp('last_login_at')->nullable();        // Datetime of the user's most recent successful login
            $table->string('last_login_ip')->nullable();           // IP address recorded at last login (IPv4 or IPv6)
            $table->timestamp('banned_at')->nullable();            // Datetime the account was banned; null = not banned
            $table->string('ban_reason')->nullable();              // Admin-provided reason for the ban

            $table->rememberToken();   // Token for "remember me" cookie-based persistent sessions
            $table->softDeletes();     // deleted_at — soft delete so records are recoverable
            $table->timestamps();      // created_at and updated_at — managed automatically by Eloquent
        });


        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
