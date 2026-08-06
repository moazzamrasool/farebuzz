<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotel_room_types', function (Blueprint $table) {
            $table->unsignedInteger('occupancy_adults')->default(2)->after('name');
            $table->unsignedInteger('occupancy_children')->default(0)->after('occupancy_adults');
            $table->string('bed_type')->nullable()->after('occupancy_children');
            $table->unsignedInteger('size_sqft')->nullable()->after('bed_type');
            $table->enum('meal_plan', ['room_only', 'breakfast', 'breakfast_dinner'])->default('room_only')->after('size_sqft');
            $table->boolean('refundable')->default(true)->after('meal_plan');
            $table->json('images')->nullable()->after('refundable');
        });
    }

    public function down(): void
    {
        Schema::table('hotel_room_types', function (Blueprint $table) {
            $table->dropColumn([
                'occupancy_adults', 'occupancy_children', 'bed_type', 'size_sqft',
                'meal_plan', 'refundable', 'images',
            ]);
        });
    }
};
