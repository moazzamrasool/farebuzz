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
        Schema::create('seo_settings', function (Blueprint $table) {
            $table->id();
            // One row per tenant — same shape as whatsapp_settings.
            $table->string('unique_id', 36)->nullable()->unique();
            // Null means "use the built-in default" — see RobotsTxtBuilder::default().
            $table->text('robots_txt')->nullable();
            // Per content-type include/priority/changefreq toggles for the sitemap;
            // null means "use SitemapGenerator::DEFAULT_CONFIG".
            $table->json('sitemap_config')->nullable();
            $table->timestamp('sitemap_generated_at')->nullable();
            $table->unsignedInteger('sitemap_url_count')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_settings');
    }
};
