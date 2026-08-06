<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('navbar_menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id', 36)->nullable()->index();
            $table->foreignId('parent_id')->nullable()->constrained('navbar_menu_items')->cascadeOnDelete();
            $table->string('label');
            $table->string('link_type'); // route | cms_page | category | custom
            $table->string('link_value');
            $table->boolean('open_in_new_tab')->default(false);
            $table->integer('sort_order')->default(0);
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('navbar_menu_items');
    }
};
