<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Single-row-per-tenant settings table, same shape as about_page — /contact-us
        // is a static Blade view with no admin/database backing today, so this covers
        // both the real contact details (currently hardcoded/missing) and its SEO.
        Schema::create('contact_page', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id', 36)->nullable()->index();

            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('support_hours')->nullable();

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
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_page');
    }
};
