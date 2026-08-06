<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('booking_type', ['package', 'hotel'])->default('package')->after('holiday_package_id');
            $table->foreignId('hotel_id')->nullable()->after('booking_type')->constrained('hotels')->nullOnDelete();
            $table->foreignId('hotel_room_type_id')->nullable()->after('hotel_id')->constrained('hotel_room_types')->nullOnDelete();
            $table->date('check_in_date')->nullable()->after('hotel_room_type_id');
            $table->date('check_out_date')->nullable()->after('check_in_date');
            $table->unsignedInteger('nights')->nullable()->after('check_out_date');
            $table->unsignedInteger('rooms')->nullable()->default(1)->after('nights');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('hotel_room_type_id');
            $table->dropConstrainedForeignId('hotel_id');
            $table->dropColumn(['booking_type', 'check_in_date', 'check_out_date', 'nights', 'rooms']);
        });
    }
};
