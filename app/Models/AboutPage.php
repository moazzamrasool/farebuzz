<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\ForSiteTenant;
use Illuminate\Database\Eloquent\Model;

class AboutPage extends Model
{
    use BelongsToTenant, ForSiteTenant;

    protected $table = 'about_page';

    // Live-count sources a stat can be bound to instead of a static admin-entered value.
    const STAT_SOURCES = [
        ''                    => 'Static value',
        'destinations_count'  => 'Destinations (live count)',
        'hotels_count'        => 'Partner hotels (live count)',
        'holiday_packages_count' => 'Holiday packages (live count)',
        'years_since_founded' => 'Years in business (from founded year)',
    ];

    protected $fillable = [
        'hero_heading',
        'hero_tagline',
        'hero_image',
        'scale_heading',
        'scale_subheading',
        'story_heading',
        'story_body',
        'story_image',
        'mission_heading',
        'mission_body',
        'mission_image',
        'founded_year',
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
        'stat1_label', 'stat1_value', 'stat1_source',
        'stat2_label', 'stat2_value', 'stat2_source',
        'stat3_label', 'stat3_value', 'stat3_source',
        'stat4_label', 'stat4_value', 'stat4_source',
        'feature1_icon', 'feature1_title', 'feature1_description',
        'feature2_icon', 'feature2_title', 'feature2_description',
        'feature3_icon', 'feature3_title', 'feature3_description',
        'cta_heading',
        'cta_text',
        'cta_button_text',
        'cta_button_link',
        'cta2_heading',
        'cta2_text',
        'cta2_button_text',
        'cta2_button_link',
        'career_heading',
        'career_text',
        'career_button_text',
        'career_button_link',
        'meta_title',
        'meta_description',
    ];

    /**
     * The 4 stat rows as [label, value, source] triples, value already resolved
     * to a display string (live DB count where source is set, else the static value).
     */
    public function resolvedStats(): array
    {
        $stats = [];

        for ($i = 1; $i <= 4; $i++) {
            $label = $this->{"stat{$i}_label"};
            $source = $this->{"stat{$i}_source"};
            $value = $this->{"stat{$i}_value"};

            if (!$label && !$value && !$source) {
                continue;
            }

            $stats[] = [
                'label' => $label,
                'value' => $source ? $this->resolveStatSource($source) : $value,
            ];
        }

        return $stats;
    }

    private function resolveStatSource(string $source): string
    {
        return match ($source) {
            'destinations_count' => (string) Destination::forSite()->where('status', 'active')->count(),
            'hotels_count' => (string) Hotel::forSite()->where('status', 'active')->count(),
            'holiday_packages_count' => (string) HolidayPackage::forSite()->where('status', 'active')->count(),
            'years_since_founded' => $this->founded_year ? (string) max(0, now()->year - $this->founded_year) : '',
            default => '',
        };
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
