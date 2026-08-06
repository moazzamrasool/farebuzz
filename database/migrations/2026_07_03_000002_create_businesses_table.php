<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_enquiry_id')->nullable()->index();
            $table->string('name');
            $table->string('owner_name');
            $table->string('email');
            $table->string('phone');
            $table->string('business_type')->nullable();
            $table->string('unique_id', 36)->unique();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
