<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_payments', function (Blueprint $table) {
            $table->id();
            // Copied from the parent booking at creation time (not tenant-global-scoped —
            // this table is always accessed via the booking relation).
            $table->string('unique_id', 36)->nullable()->index();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();

            $table->string('gateway')->default('payu');
            $table->string('mode', 10); // snapshot of PAYU_MODE at the time of this transaction
            $table->string('gateway_txn_id')->unique();
            $table->string('gateway_payment_id')->nullable(); // PayU's mihpayid, once known
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('INR');
            $table->enum('status', ['initiated', 'success', 'failed', 'cancelled'])->default('initiated');
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->boolean('hash_verified')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_payments');
    }
};
