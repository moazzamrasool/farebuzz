<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Public-safe identifier for a user, independent of the internal
            // auto-increment id. Not to be confused with the "unique_id" column
            // used elsewhere in this app to mean "which tenant/company owns this
            // row" (see App\Models\Concerns\BelongsToTenant) — a User isn't owned
            // by a single company, so that name would be misleading here.
            $table->string('public_id', 36)->nullable()->after('id');

            $table->string('email_verification_token', 64)->nullable()->after('email_verified_at');

            // pending_verification | active | suspended
            $table->string('status')->default('pending_verification')->after('email_verification_token');
        });

        // Backfill existing rows before enforcing uniqueness on public_id.
        DB::table('users')->whereNull('public_id')->orderBy('id')->each(function ($user) {
            DB::table('users')->where('id', $user->id)->update([
                'public_id' => (string) Str::uuid(),
                // Users who already had a verified email (e.g. via social login)
                // shouldn't be locked out by this migration.
                'status' => $user->email_verified_at ? 'active' : 'pending_verification',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unique('public_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['public_id']);
            $table->dropIndex(['status']);
            $table->dropColumn(['public_id', 'email_verification_token', 'status']);
        });
    }
};
