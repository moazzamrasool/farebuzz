<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $table->dropForeign(['travel_category_id']);
            $table->dropColumn('travel_category_id');
            $table->string('local')->default('domestic')->after('slug');
            $table->text('meta')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $table->dropColumn(['local', 'meta']);
            $table->foreignId('travel_category_id')->nullable()->constrained('travel_categories')->restrictOnDelete();
        });
    }
};
