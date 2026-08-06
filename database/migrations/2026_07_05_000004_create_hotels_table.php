<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id', 36)->nullable()->index();
            $table->string('name');
            $table->unsignedTinyInteger('star_rating')->default(0);
            $table->string('address')->nullable();
            $table->text('description')->nullable();
            $table->decimal('rating_score', 3, 1)->nullable();
            $table->unsignedInteger('review_count')->default(0);
            $table->string('cover_image')->nullable();
            $table->json('gallery_images')->nullable();
            $table->string('status')->default('active');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('hotel_amenity', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id', 36)->nullable()->index();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->foreignId('amenity_id')->constrained('amenities')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_amenity');
        Schema::dropIfExists('hotels');
    }
};
