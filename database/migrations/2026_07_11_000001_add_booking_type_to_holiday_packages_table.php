<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('holiday_packages', function (Blueprint $table) {
            // enquiry_only: detail page shows only "Enquire Now"; book_enquiry: shows both "Book Now" and "Enquire Now".
            $table->enum('booking_type', ['enquiry_only', 'book_enquiry'])->default('enquiry_only')->after('discounted_price');
        });
    }

    public function down(): void
    {
        Schema::table('holiday_packages', function (Blueprint $table) {
            $table->dropColumn('booking_type');
        });
    }
};
