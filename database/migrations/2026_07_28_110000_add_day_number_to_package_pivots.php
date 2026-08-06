<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('holiday_package_hotel', function (Blueprint $table) {
            $table->unsignedInteger('day_number')->nullable()->after('hotel_id');
        });

        Schema::table('package_activity', function (Blueprint $table) {
            $table->unsignedInteger('day_number')->nullable()->after('activity_id');
        });
    }

    public function down(): void
    {
        Schema::table('holiday_package_hotel', function (Blueprint $table) {
            $table->dropColumn('day_number');
        });

        Schema::table('package_activity', function (Blueprint $table) {
            $table->dropColumn('day_number');
        });
    }
};
