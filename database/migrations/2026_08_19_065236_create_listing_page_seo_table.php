<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // One row per tenant per general listing page (india-packages, international-packages,
    // hotels, activities, ...) — same field set as homepage_seo, keyed by page_key instead
    // of being a dedicated single-purpose table, so a future listing page needs no migration.
    public function up(): void
    {
        Schema::create('listing_page_seo', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id', 36)->nullable()->index();
            $table->string('page_key');

            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('focus_keyword')->nullable();
            $table->string('tags')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->boolean('robots_index')->default(true);
            $table->boolean('robots_follow')->default(true);

            $table->timestamps();

            $table->unique(['unique_id', 'page_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_page_seo');
    }
};
