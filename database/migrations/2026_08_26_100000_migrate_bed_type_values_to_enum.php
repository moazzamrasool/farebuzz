<?php

use App\Enums\BedType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// hotel_room_types.bed_type stays a plain nullable string column (same convention as
// LeadStatus's status column) — BedType is the PHP-side source of truth. Existing rows
// stored the human label ("King Bed") rather than a snake_case value, so normalize them
// before the model cast starts treating the column as BedType-backed.
return new class extends Migration
{
    public function up(): void
    {
        foreach (BedType::options() as $value => $label) {
            DB::table('hotel_room_types')->where('bed_type', $label)->update(['bed_type' => $value]);
        }
    }

    public function down(): void
    {
        foreach (BedType::options() as $value => $label) {
            DB::table('hotel_room_types')->where('bed_type', $value)->update(['bed_type' => $label]);
        }
    }
};
