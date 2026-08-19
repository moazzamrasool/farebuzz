<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Separate from the existing meta_title/meta_description columns, which back
    // the /destinations/{slug} overview page — these back the distinct
    // /destinations/{slug}/packages listing page (see PackageController::byDestination).
    public function up(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $table->string('packages_meta_title')->nullable()->after('meta_description');
            $table->text('packages_meta_description')->nullable()->after('packages_meta_title');
        });
    }

    public function down(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $table->dropColumn(['packages_meta_title', 'packages_meta_description']);
        });
    }
};
