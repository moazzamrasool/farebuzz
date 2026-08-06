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
        Schema::table('about_page', function (Blueprint $table) {
            $table->string('hero_image')->nullable()->after('hero_tagline');

            $table->string('scale_heading')->nullable()->after('founded_year');
            $table->string('scale_subheading')->nullable()->after('scale_heading');

            $table->string('team_heading')->nullable()->after('mission_image');
            $table->string('team_subheading')->nullable()->after('team_heading');

            $table->string('growth_heading')->nullable()->after('team_subheading');
            $table->string('growth_subheading')->nullable()->after('growth_heading');

            $table->string('timeline_heading')->nullable()->after('growth_subheading');
            $table->string('timeline_subheading')->nullable()->after('timeline_heading');

            $table->string('life_heading')->nullable()->after('timeline_subheading');
            $table->string('life_subheading')->nullable()->after('life_heading');
            $table->text('life_body')->nullable()->after('life_subheading');
            $table->string('life_image')->nullable()->after('life_body');

            $table->string('gallery_heading')->nullable()->after('life_image');

            $table->string('testimonials_heading')->nullable()->after('gallery_heading');

            $table->string('press_heading')->nullable()->after('testimonials_heading');

            $table->string('impact_heading')->nullable()->after('press_heading');
            $table->text('impact_body')->nullable()->after('impact_heading');
            $table->string('impact_image')->nullable()->after('impact_body');

            $table->string('awards_heading')->nullable()->after('impact_image');

            $table->string('cta2_heading')->nullable()->after('cta_button_link');
            $table->string('cta2_text')->nullable()->after('cta2_heading');
            $table->string('cta2_button_text')->nullable()->after('cta2_text');
            $table->string('cta2_button_link')->nullable()->after('cta2_button_text');

            $table->string('career_heading')->nullable()->after('cta2_button_link');
            $table->string('career_text')->nullable()->after('career_heading');
            $table->string('career_button_text')->nullable()->after('career_text');
            $table->string('career_button_link')->nullable()->after('career_button_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('about_page', function (Blueprint $table) {
            $table->dropColumn([
                'hero_image',
                'scale_heading', 'scale_subheading',
                'team_heading', 'team_subheading',
                'growth_heading', 'growth_subheading',
                'timeline_heading', 'timeline_subheading',
                'life_heading', 'life_subheading', 'life_body', 'life_image',
                'gallery_heading',
                'testimonials_heading',
                'press_heading',
                'impact_heading', 'impact_body', 'impact_image',
                'awards_heading',
                'cta2_heading', 'cta2_text', 'cta2_button_text', 'cta2_button_link',
                'career_heading', 'career_text', 'career_button_text', 'career_button_link',
            ]);
        });
    }
};
