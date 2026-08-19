<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\ForSiteTenant;
use Illuminate\Database\Eloquent\Model;

class AboutPage extends Model
{
    use BelongsToTenant, ForSiteTenant;

    protected $table = 'about_page';

    protected $fillable = [
        'hero_heading',
        'hero_tagline',
        'hero_image',
        'story_heading',
        'story_body',
        'story_image',
        'mission_heading',
        'mission_body',
        'mission_image',
        'team_heading',
        'team_subheading',
        'growth_heading',
        'growth_subheading',
        'timeline_heading',
        'timeline_subheading',
        'life_heading',
        'life_subheading',
        'life_body',
        'life_image',
        'gallery_heading',
        'testimonials_heading',
        'press_heading',
        'impact_heading',
        'impact_body',
        'impact_image',
        'awards_heading',
        'feature1_icon', 'feature1_title', 'feature1_description',
        'feature2_icon', 'feature2_title', 'feature2_description',
        'feature3_icon', 'feature3_title', 'feature3_description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'focus_keyword',
        'tags',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image',
        'robots_index',
        'robots_follow',
    ];

    protected function casts(): array
    {
        return [
            'robots_index' => 'boolean',
            'robots_follow' => 'boolean',
        ];
    }

    /**
     * The 3 feature cards as [icon, title, description] triples, skipping empty ones.
     */
    public function resolvedFeatures(): array
    {
        $features = [];

        for ($i = 1; $i <= 3; $i++) {
            $title = $this->{"feature{$i}_title"};
            if (!$title) {
                continue;
            }

            $features[] = [
                'icon' => $this->{"feature{$i}_icon"} ?: 'bi-star',
                'title' => $title,
                'description' => $this->{"feature{$i}_description"},
            ];
        }

        return $features;
    }
}
