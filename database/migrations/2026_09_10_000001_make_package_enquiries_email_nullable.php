<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Standalone landing pages (e.g. the Kashmir-from-Bangalore lander) capture name/phone
// only, no email — but package_enquiries.email was created NOT NULL for the
// "Enquire Now" flow, which always has an email. Widen it rather than adding a second
// enquiries table; existing rows/queries are unaffected since none relied on NOT NULL.
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE package_enquiries MODIFY email VARCHAR(255) NULL');
    }

    public function down(): void
    {
        DB::table('package_enquiries')->whereNull('email')->update(['email' => '']);
        DB::statement('ALTER TABLE package_enquiries MODIFY email VARCHAR(255) NOT NULL');
    }
};
