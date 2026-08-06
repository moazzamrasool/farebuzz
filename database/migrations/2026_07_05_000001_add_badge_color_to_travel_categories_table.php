<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('travel_categories', function (Blueprint $table) {
            $table->string('badge_color')->nullable()->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('travel_categories', function (Blueprint $table) {
            $table->dropColumn('badge_color');
        });
    }
};
