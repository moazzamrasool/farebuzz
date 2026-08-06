<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\ForSiteTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HomepageSection extends Model
{
    use BelongsToTenant, ForSiteTenant;

    protected $fillable = [
        'key',
        'name',
        'heading',
        'subheading',
        'extra',
        'sort_order',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'extra' => 'array',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(HomepageSectionItem::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Active items for this section, optionally restricted to one sub-tab
     * (group_key), ordered for display. Used by every homepage partial.
     */
    public function activeItems(?string $groupKey = null): \Illuminate\Database\Eloquent\Collection
    {
        return $this->items()
            ->when($groupKey !== null, fn ($query) => $query->where('group_key', $groupKey))
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->get();
    }
}
