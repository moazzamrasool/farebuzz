<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\ForSiteTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use BelongsToTenant, ForSiteTenant;

    const CATEGORIES = [
        'destinations' => 'Destinations',
        'travel-tips'  => 'Travel Tips',
        'offers'       => 'Offers',
        'general'      => 'General',
    ];

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'category',
        'author_name',
        'status',
        'is_featured',
        'published_at',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'canonical_url',
        'focus_keyword',
        'tags',
        'og_title',
        'og_description',
        'robots_index',
        'robots_follow',
        'reading_time',
    ];

    protected function casts(): array
    {
        return [
            'is_featured'  => 'boolean',
            'published_at' => 'datetime',
            'robots_index'  => 'boolean',
            'robots_follow' => 'boolean',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->where('published_at', '<=', now());
    }

    // Admin-entered reading_time wins when set; otherwise estimated from word count
    // at 200 wpm, same as before this column existed.
    public function getReadTimeAttribute(): string
    {
        if ($this->reading_time) {
            return $this->reading_time.' min read';
        }

        $words = str_word_count(strip_tags((string) $this->content));
        $minutes = max(1, (int) ceil($words / 200));

        return $minutes.' min read';
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst($this->category);
    }
}
