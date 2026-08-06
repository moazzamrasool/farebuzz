<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id', 36)->nullable();
            $table->string('wa_phone');
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->text('requirement')->nullable();
            $table->json('conversation_history')->nullable();
            $table->foreignId('matched_holiday_package_id')->nullable()
                ->constrained('holiday_packages')->nullOnDelete();
            $table->foreignId('package_enquiry_id')->nullable()
                ->constrained('package_enquiries')->nullOnDelete();
            $table->string('status')->default('in_progress');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->index(['unique_id', 'wa_phone', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_sessions');
    }
};
