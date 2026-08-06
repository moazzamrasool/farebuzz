<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('holiday_package_hotel', function (Blueprint $table) {
            $table->foreignId('room_type_id')->nullable()->after('hotel_id')->constrained('hotel_room_types')->nullOnDelete();
            $table->decimal('price', 10, 2)->nullable()->after('room_type_id');
            $table->boolean('is_optional')->default(true)->after('price');
            $table->integer('nights')->default(1)->after('is_optional');
        });
    }

    public function down(): void
    {
        Schema::table('holiday_package_hotel', function (Blueprint $table) {
            $table->dropConstrainedForeignId('room_type_id');
            $table->dropColumn(['price', 'is_optional', 'nights']);
        });
    }
};
