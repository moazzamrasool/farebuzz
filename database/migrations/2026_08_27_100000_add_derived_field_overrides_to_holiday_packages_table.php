<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Nights/Days/Hotel Category/Meals are now auto-derived from the itinerary and
// attached hotels (see HolidayPackage::applyDerivedFields()). Hotel Category and
// Meals can still be manually overridden per package; these two flags record
// that choice so recompute knows to leave the admin's typed text alone. Nights/
// Days have no override — the itinerary is always authoritative for those.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('holiday_packages', function (Blueprint $table) {
            $table->boolean('hotel_category_overridden')->default(false)->after('hotel_category');
            $table->boolean('meals_overridden')->default(false)->after('meals');
        });
    }

    public function down(): void
    {
        Schema::table('holiday_packages', function (Blueprint $table) {
            $table->dropColumn(['hotel_category_overridden', 'meals_overridden']);
        });
    }
};
