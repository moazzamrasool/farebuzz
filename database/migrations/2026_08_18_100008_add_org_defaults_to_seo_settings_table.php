<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seo_settings', function (Blueprint $table) {
            // Site-wide fallbacks: used when a page has no og_image of its own, and to
            // fill the Organization JSON-LD block that appears on every page.
            $table->string('default_og_image')->nullable()->after('sitemap_url_count');
            $table->string('organization_name')->nullable()->after('default_og_image');
            $table->string('organization_logo')->nullable()->after('organization_name');
            $table->json('social_links')->nullable()->after('organization_logo');
        });
    }

    public function down(): void
    {
        Schema::table('seo_settings', function (Blueprint $table) {
            $table->dropColumn(['default_og_image', 'organization_name', 'organization_logo', 'social_links']);
        });
    }
};
