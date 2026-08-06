<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tracking_scripts', function (Blueprint $table) {
            $table->id();
            // One row per tenant — same shape as whatsapp_settings.
            $table->string('unique_id', 36)->nullable()->unique();
            $table->text('header_script')->nullable();
            $table->boolean('header_enabled')->default(false);
            $table->text('body_script')->nullable();
            $table->boolean('body_enabled')->default(false);
            $table->text('footer_script')->nullable();
            $table->boolean('footer_enabled')->default(false);
            // Who last saved this — raw script injection is powerful, so it's audited.
            $table->foreignId('updated_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_scripts');
    }
};
