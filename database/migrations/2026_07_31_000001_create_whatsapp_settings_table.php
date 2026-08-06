<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_settings', function (Blueprint $table) {
            $table->id();
            // One row per tenant — same shape as ai_package_settings.
            $table->string('unique_id', 36)->nullable()->unique();
            $table->string('phone_number_id')->nullable();
            $table->text('access_token')->nullable();
            $table->string('business_account_id')->nullable();
            $table->boolean('enabled')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_settings');
    }
};
