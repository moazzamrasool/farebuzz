<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('holiday_packages', function (Blueprint $table) {
            $table->string('focus_keyword')->nullable()->after('meta_keywords');
            $table->longText('seo_content')->nullable()->after('overview');
        });
    }

    public function down(): void
    {
        Schema::table('holiday_packages', function (Blueprint $table) {
            $table->dropColumn(['focus_keyword', 'seo_content']);
        });
    }
};
