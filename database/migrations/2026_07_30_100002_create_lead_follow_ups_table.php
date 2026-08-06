<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_follow_ups', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id', 36)->nullable()->index();
            $table->string('leadable_type');
            $table->unsignedBigInteger('leadable_id');
            $table->index(['leadable_type', 'leadable_id']);
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('assigned_admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->dateTime('due_at')->index();
            $table->text('note')->nullable();
            $table->string('status')->default('pending');
            $table->dateTime('completed_at')->nullable();
            // Guards the reminder scheduler from sending more than one email per follow-up.
            $table->boolean('reminder_sent')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_follow_ups');
    }
};
