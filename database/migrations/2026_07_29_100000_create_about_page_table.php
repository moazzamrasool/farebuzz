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
        // Single-row-per-tenant settings table (mirrors search_tab_settings /
        // homepage_sections in spirit) — the About Us layout is fixed, not a
        // reorderable list of sections, so one row of named columns per company.
        Schema::create('about_page', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id', 36)->nullable()->index();

            $table->string('hero_heading')->nullable();
            $table->string('hero_tagline')->nullable();

            $table->string('story_heading')->nullable();
            $table->longText('story_body')->nullable();
            $table->string('story_image')->nullable();

            $table->string('mission_heading')->nullable();
            $table->longText('mission_body')->nullable();
            $table->string('mission_image')->nullable();

            $table->unsignedSmallInteger('founded_year')->nullable();

            for ($i = 1; $i <= 4; $i++) {
                $table->string("stat{$i}_label")->nullable();
                $table->string("stat{$i}_value")->nullable();
                // Null = use stat{i}_value as-is. Otherwise a live DB count backs the number.
                $table->string("stat{$i}_source")->nullable();
            }

            for ($i = 1; $i <= 3; $i++) {
                $table->string("feature{$i}_icon")->nullable();
                $table->string("feature{$i}_title")->nullable();
                $table->text("feature{$i}_description")->nullable();
            }

            $table->string('cta_heading')->nullable();
            $table->string('cta_text')->nullable();
            $table->string('cta_button_text')->nullable();
            $table->string('cta_button_link')->nullable();

            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_page');
    }
};
