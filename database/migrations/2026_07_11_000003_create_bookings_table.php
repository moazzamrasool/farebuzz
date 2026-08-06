<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id', 36)->nullable()->index();
            $table->string('booking_reference')->unique();
            $table->foreignId('holiday_package_id')->nullable()->constrained('holiday_packages')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Snapshots — the booking must stay intact even if the package is later edited/removed.
            $table->string('package_title');
            $table->string('package_slug');
            $table->string('departure_city')->nullable();
            $table->date('travel_date');
            $table->string('room_type_name')->nullable();
            $table->decimal('room_type_price', 10, 2)->nullable();
            $table->unsignedInteger('adults')->default(1);
            $table->unsignedInteger('children')->default(0);

            // Traveller details
            $table->string('traveller_name');
            $table->string('traveller_email');
            $table->string('traveller_phone');
            $table->text('traveller_address');
            $table->text('special_requests')->nullable();
            $table->string('gst_number')->nullable();

            // Price breakdown — always computed server-side, never trusted from the client.
            $table->decimal('base_fare', 10, 2);
            $table->decimal('taxes_fee', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);

            $table->enum('status', ['pending', 'confirmed', 'failed', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
