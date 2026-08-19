<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\ForSiteTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AboutPageItem extends Model
{
    use BelongsToTenant, ForSiteTenant;

    // Every repeatable-list section on the About Us page.
    const SECTIONS = [
        'mission_card' => 'Mission Card',
        'team_member' => 'Team Member',
        'growth_stat' => 'Growth Stat',
        'timeline' => 'Timeline Milestone',
        'gallery' => 'Gallery Photo',
        'testimonial' => 'Testimonial',
        'press' => 'Press Logo',
        'award' => 'Award',
    ];

    protected $fillable = [
        'section_key',
        'image',
        'title',
        'subtitle',
        'description',
        'link',
        'sort_order',
        'status',
    ];

    public function scopeSection(Builder $query, string $key): Builder
    {
        return $query->where('section_key', $key);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }
}
