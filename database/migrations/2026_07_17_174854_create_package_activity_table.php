<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_activity', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id', 36)->nullable()->index();
            $table->foreignId('holiday_package_id')->constrained('holiday_packages')->cascadeOnDelete();
            $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
            $table->decimal('price', 10, 2)->nullable();
            $table->boolean('is_optional')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_activity');
    }
};
