<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Extends package_enquiries in place into a full CRM "lead" record instead of
// introducing a separate leads table, so nothing that already depends on
// PackageEnquiry/package_enquiries breaks.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('package_enquiries', function (Blueprint $table) {
            $table->foreignId('assigned_admin_id')->nullable()->after('user_id')->constrained('admins')->nullOnDelete();
            $table->decimal('budget', 12, 2)->nullable()->after('travellers');
            $table->string('source')->nullable()->default('website')->after('budget');
            $table->text('lost_reason')->nullable()->after('message');
            $table->timestamp('next_follow_up_at')->nullable()->after('status')->index();
            $table->timestamp('last_activity_at')->nullable()->after('next_follow_up_at')->index();
        });

        // Widen status from enum('new','contacted','closed') to a free string so it can
        // hold the new 9-stage LeadStatus values. Raw DDL avoids pulling in doctrine/dbal
        // (not installed) just for one column type change.
        DB::statement("ALTER TABLE package_enquiries MODIFY status VARCHAR(30) NOT NULL DEFAULT 'new'");

        // Data-migrate existing rows onto the new LeadStatus values. "closed" was the old
        // model's only terminal state (no won/lost distinction ever existed), so it is
        // mapped to "converted" rather than "lost" — assuming a fabricated loss for
        // historical rows would misrepresent them more than assuming they were completed.
        DB::table('package_enquiries')->where('status', 'closed')->update(['status' => 'converted']);
        // 'new' and 'contacted' values are already valid LeadStatus values — no change needed.
    }

    public function down(): void
    {
        // Best-effort, lossy collapse of the 9-stage values back onto the old 3-state model.
        DB::table('package_enquiries')->where('status', 'converted')->update(['status' => 'closed']);
        DB::table('package_enquiries')->whereIn('status', ['lost', 'junk'])->update(['status' => 'closed']);
        DB::table('package_enquiries')->whereIn('status', [
            'interested', 'quotation_sent', 'negotiation', 'follow_up',
        ])->update(['status' => 'contacted']);

        DB::statement("ALTER TABLE package_enquiries MODIFY status ENUM('new','contacted','closed') NOT NULL DEFAULT 'new'");

        Schema::table('package_enquiries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assigned_admin_id');
            $table->dropColumn(['budget', 'source', 'lost_reason', 'next_follow_up_at', 'last_activity_at']);
        });
    }
};
