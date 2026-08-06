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
        Schema::table('hotels', function (Blueprint $table) {
            $table->foreignId('destination_id')->nullable()->after('id')->constrained('destinations')->nullOnDelete();
            // Nullable+unique is safe in MySQL (multiple NULLs are allowed in a unique
            // index) — avoids a separate ->change() step, which would need doctrine/dbal.
            $table->string('slug')->nullable()->unique()->after('name');
            $table->decimal('latitude', 10, 7)->nullable()->after('address');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('check_in_time', 10)->nullable()->after('longitude');
            $table->string('check_out_time', 10)->nullable()->after('check_in_time');
            $table->text('property_rules')->nullable()->after('check_out_time');
        });

        // Backfill a unique slug for every hotel created before this column existed —
        // the unique index below would otherwise fail on the very next migration step.
        $usedSlugs = [];
        foreach (DB::table('hotels')->orderBy('id')->get(['id', 'name']) as $hotel) {
            $base = Str::slug($hotel->name) ?: 'hotel';
            $slug = $base;
            $suffix = 1;
            while (in_array($slug, $usedSlugs, true)) {
                $slug = "{$base}-{$suffix}";
                $suffix++;
            }
            $usedSlugs[] = $slug;
            DB::table('hotels')->where('id', $hotel->id)->update(['slug' => $slug]);
        }
    }

    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropConstrainedForeignId('destination_id');
            $table->dropUnique(['slug']);
            $table->dropColumn(['slug', 'latitude', 'longitude', 'check_in_time', 'check_out_time', 'property_rules']);
        });
    }
};
