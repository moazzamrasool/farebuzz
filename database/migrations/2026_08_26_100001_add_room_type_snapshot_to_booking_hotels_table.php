<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Day-wise package hotel add-ons only baked room type into the free-text `name` column
// (e.g. "Hotel X — Deluxe Room") with no normalized/queryable room-type snapshot. Add an
// FK plus discrete snapshot fields so invoices/emails/admin views can render room type and
// bed type independently of `name`, consistent with how `bookings` already snapshots the
// standalone hotel-booking room type.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_hotels', function (Blueprint $table) {
            $table->foreignId('hotel_room_type_id')->nullable()->after('hotel_id')->constrained('hotel_room_types')->nullOnDelete();
            $table->string('room_type_name')->nullable()->after('hotel_room_type_id');
            $table->string('bed_type')->nullable()->after('room_type_name');
        });
    }

    public function down(): void
    {
        Schema::table('booking_hotels', function (Blueprint $table) {
            $table->dropConstrainedForeignId('hotel_room_type_id');
            $table->dropColumn(['room_type_name', 'bed_type']);
        });
    }
};
