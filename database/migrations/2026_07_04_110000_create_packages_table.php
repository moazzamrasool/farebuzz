<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id', 36)->nullable()->index();
            $table->foreignId('travel_category_id')->constrained('travel_categories')->restrictOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->default('domestic');
            $table->text('description')->nullable();
            $table->text('meta')->nullable();
            $table->string('image')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('status')->default('active');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
