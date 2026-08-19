<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // The stats bar ("Proof We Operate at Scale"), primary CTA, secondary CTA
        // ("Partner With FareBuzzer") and career banner sections were removed from
        // the public About Us page — dropping their now-unused columns.
        Schema::table('about_page', function (Blueprint $table) {
            $table->dropColumn([
                'scale_heading', 'scale_subheading',
                'founded_year',
                'stat1_label', 'stat1_value', 'stat1_source',
                'stat2_label', 'stat2_value', 'stat2_source',
                'stat3_label', 'stat3_value', 'stat3_source',
                'stat4_label', 'stat4_value', 'stat4_source',
                'cta_heading', 'cta_text', 'cta_button_text', 'cta_button_link',
                'cta2_heading', 'cta2_text', 'cta2_button_text', 'cta2_button_link',
                'career_heading', 'career_text', 'career_button_text', 'career_button_link',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('about_page', function (Blueprint $table) {
            $table->string('scale_heading')->nullable();
            $table->string('scale_subheading')->nullable();
            $table->unsignedSmallInteger('founded_year')->nullable();

            for ($i = 1; $i <= 4; $i++) {
                $table->string("stat{$i}_label")->nullable();
                $table->string("stat{$i}_value")->nullable();
                $table->string("stat{$i}_source")->nullable();
            }

            $table->string('cta_heading')->nullable();
            $table->string('cta_text')->nullable();
            $table->string('cta_button_text')->nullable();
            $table->string('cta_button_link')->nullable();

            $table->string('cta2_heading')->nullable();
            $table->string('cta2_text')->nullable();
            $table->string('cta2_button_text')->nullable();
            $table->string('cta2_button_link')->nullable();

            $table->string('career_heading')->nullable();
            $table->string('career_text')->nullable();
            $table->string('career_button_text')->nullable();
            $table->string('career_button_link')->nullable();
        });
    }
};
