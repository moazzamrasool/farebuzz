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
        // Generic repeatable rows for every list-shaped About Us section (mission
        // cards, team members, growth stats, timeline milestones, gallery photos,
        // testimonials, press logos, awards, trust badges) — one flat shape
        // (image/title/subtitle/description/link) differentiated by section_key,
        // same spirit as HomepageSectionItem but without a parent section row
        // since About Us's sections are fixed, not admin-managed.
        Schema::create('about_page_items', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id', 36)->nullable()->index();
            $table->string('section_key');
            $table->string('image')->nullable();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('link')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('status')->default('active');
            $table->timestamps();

            $table->index(['unique_id', 'section_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_page_items');
    }
};
