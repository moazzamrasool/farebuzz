<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // room_type_price already snapshots the sell/discounted price charged.
            $table->decimal('room_type_original_price', 10, 2)->nullable()->after('room_type_price');
            $table->unsignedTinyInteger('room_type_discount_percent')->nullable()->after('room_type_original_price');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['room_type_original_price', 'room_type_discount_percent']);
        });
    }
};
