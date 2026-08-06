<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            // Company-level block, independent of the account-level `status` column.
            // Meaningful on tier=admin rows (the company itself); tier=user rows inherit
            // it via Admin::isCompanySuspended() looking up their company's admin row.
            $table->boolean('is_blocked')->default(false)->after('status');
            $table->timestamp('blocked_at')->nullable()->after('is_blocked');
            $table->foreignId('blocked_by')->nullable()->after('blocked_at')
                ->constrained('admins')->nullOnDelete();
            $table->text('blocked_reason')->nullable()->after('blocked_by');
            $table->timestamp('unblocked_at')->nullable()->after('blocked_reason');
            $table->foreignId('unblocked_by')->nullable()->after('unblocked_at')
                ->constrained('admins')->nullOnDelete();
            $table->text('unblocked_reason')->nullable()->after('unblocked_by');

            $table->index('is_blocked');
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropForeign(['blocked_by']);
            $table->dropForeign(['unblocked_by']);
            $table->dropIndex(['is_blocked']);
            $table->dropColumn([
                'is_blocked',
                'blocked_at',
                'blocked_by',
                'blocked_reason',
                'unblocked_at',
                'unblocked_by',
                'unblocked_reason',
            ]);
        });
    }
};
