<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('holiday_packages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('travel_category_id');
            $table->dropColumn([
                'short_description',
                'full_description',
                'inclusions',
                'exclusions',
                'cover_image',
                'gallery_images',
            ]);

            $table->string('hotel_category')->nullable()->after('days');
            $table->string('meals')->nullable()->after('hotel_category');
            $table->string('language')->nullable()->after('meals');
            $table->boolean('is_best_seller')->default(false)->after('featured');
            $table->longText('overview')->nullable()->after('language');
            $table->integer('sort_order')->default(0)->after('is_best_seller');
        });
    }

    public function down(): void
    {
        Schema::table('holiday_packages', function (Blueprint $table) {
            $table->dropColumn([
                'hotel_category',
                'meals',
                'language',
                'overview',
                'is_best_seller',
                'sort_order',
            ]);

            $table->foreignId('travel_category_id')->nullable()->after('id')
                ->constrained('travel_categories')->restrictOnDelete();
            $table->text('short_description')->nullable();
            $table->longText('full_description')->nullable();
            $table->text('inclusions')->nullable();
            $table->text('exclusions')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('gallery_images')->nullable();
        });
    }
};
