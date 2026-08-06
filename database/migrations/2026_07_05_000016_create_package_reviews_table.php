<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_reviews', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id', 36)->nullable()->index();
            $table->foreignId('holiday_package_id')->constrained('holiday_packages')->cascadeOnDelete();
            $table->string('reviewer_name');
            $table->decimal('rating', 2, 1);
            $table->decimal('hotels_rating', 2, 1)->nullable();
            $table->decimal('sightseeing_rating', 2, 1)->nullable();
            $table->decimal('food_rating', 2, 1)->nullable();
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
        Schema::dropIfExists('package_reviews');
    }
};
