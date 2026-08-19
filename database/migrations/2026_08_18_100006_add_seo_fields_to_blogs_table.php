<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->string('focus_keyword')->nullable()->after('canonical_url');
            $table->string('tags')->nullable()->after('focus_keyword');
            $table->string('og_title')->nullable()->after('tags');
            $table->text('og_description')->nullable()->after('og_title');
            $table->boolean('robots_index')->default(true)->after('og_description');
            $table->boolean('robots_follow')->default(true)->after('robots_index');
            $table->unsignedSmallInteger('reading_time')->nullable()->after('robots_follow');
        });
    }

    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn(['focus_keyword', 'tags', 'og_title', 'og_description', 'robots_index', 'robots_follow', 'reading_time']);
        });
    }
};
