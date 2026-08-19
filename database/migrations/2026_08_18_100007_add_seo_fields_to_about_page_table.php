<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('about_page', function (Blueprint $table) {
            $table->string('meta_keywords')->nullable()->after('meta_description');
            $table->string('focus_keyword')->nullable()->after('meta_keywords');
            $table->string('tags')->nullable()->after('focus_keyword');
            $table->string('canonical_url')->nullable()->after('tags');
            $table->string('og_title')->nullable()->after('canonical_url');
            $table->text('og_description')->nullable()->after('og_title');
            $table->string('og_image')->nullable()->after('og_description');
            $table->boolean('robots_index')->default(true)->after('og_image');
            $table->boolean('robots_follow')->default(true)->after('robots_index');
        });
    }

    public function down(): void
    {
        Schema::table('about_page', function (Blueprint $table) {
            $table->dropColumn(['meta_keywords', 'focus_keyword', 'tags', 'canonical_url', 'og_title', 'og_description', 'og_image', 'robots_index', 'robots_follow']);
        });
    }
};
