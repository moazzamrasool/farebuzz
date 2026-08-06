<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// The full activity timeline for a lead — status changes, notes, calls, emails,
// WhatsApp messages, follow-up scheduled/completed, assignment changes all log here.
// Uses morph columns (leadable_type/leadable_id) rather than a hard package_enquiry_id
// FK so a future HotelEnquiry (or other lead source) can share this same table.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_activities', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id', 36)->nullable()->index();
            $table->string('leadable_type');
            $table->unsignedBigInteger('leadable_id');
            $table->index(['leadable_type', 'leadable_id']);
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('type');
            $table->string('channel')->nullable();
            $table->text('description');
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_activities');
    }
};
