<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            // 'category' (free-text) is kept alongside this for now — see backfill command.
            // It will be dropped in a follow-up migration once activity_category_id is confirmed stable.
            $table->foreignId('activity_category_id')->nullable()->after('travel_category_id')
                ->constrained('activity_categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropConstrainedForeignId('activity_category_id');
        });
    }
};
