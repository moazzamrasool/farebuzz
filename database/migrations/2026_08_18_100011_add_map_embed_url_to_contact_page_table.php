<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_page', function (Blueprint $table) {
            // Full Google Maps embed URL, pasted from Maps' "Share > Embed a map" — takes
            // priority over auto-generating one from the address field when both are set.
            $table->string('map_embed_url', 2048)->nullable()->after('support_hours');
        });
    }

    public function down(): void
    {
        Schema::table('contact_page', function (Blueprint $table) {
            $table->dropColumn('map_embed_url');
        });
    }
};
