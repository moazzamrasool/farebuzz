<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_reviews', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id', 36)->nullable()->index();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->string('reviewer_name');
            $table->decimal('rating', 2, 1);
            $table->decimal('location_rating', 2, 1)->nullable();
            $table->decimal('cleanliness_rating', 2, 1)->nullable();
            $table->decimal('service_rating', 2, 1)->nullable();
            $table->decimal('value_rating', 2, 1)->nullable();
            $table->text('comment')->nullable();
            $table->date('review_date');
            $table->boolean('verified')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_reviews');
    }
};
