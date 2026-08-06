<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_hotels', function (Blueprint $table) {
            $table->unsignedInteger('day_number')->nullable()->after('hotel_id');
            $table->date('stay_date')->nullable()->after('day_number');
        });

        Schema::table('booking_activities', function (Blueprint $table) {
            $table->unsignedInteger('day_number')->nullable()->after('activity_id');
            $table->date('activity_date')->nullable()->after('day_number');
        });
    }

    public function down(): void
    {
        Schema::table('booking_hotels', function (Blueprint $table) {
            $table->dropColumn(['day_number', 'stay_date']);
        });

        Schema::table('booking_activities', function (Blueprint $table) {
            $table->dropColumn(['day_number', 'activity_date']);
        });
    }
};
