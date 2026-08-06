<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            // Tenant/company key. NULL only for the Super Admin row(s) — every Admin
            // (company owner) and User (sub-admin) row carries the same unique_id.
            $table->string('unique_id', 36)->nullable()->after('id');

            // super_admin | admin | user — deliberately not named "role" to avoid
            // ambiguity with Spatie's own Role model once HasRoles is mixed in.
            $table->string('tier')->default('user')->after('unique_id');

            $table->foreignId('created_by')->nullable()->after('tier')
                ->constrained('admins')->nullOnDelete();

            // active | inactive — same vocabulary as Destination/TravelCategory/etc.
            $table->string('status')->default('active')->after('created_by');

            $table->index(['unique_id', 'tier']);
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropIndex(['unique_id', 'tier']);
            $table->dropColumn(['unique_id', 'tier', 'created_by', 'status']);
        });
    }
};
