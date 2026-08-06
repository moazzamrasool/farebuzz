<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\ForSiteTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Route;

class NavbarMenuItem extends Model
{
    use BelongsToTenant, ForSiteTenant;

    protected $fillable = [
        'parent_id',
        'label',
        'link_type',
        'link_value',
        'open_in_new_tab',
        'sort_order',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'open_in_new_tab' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeTopLevel(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    // Resolves link_type + link_value into an actual frontend URL. 'route' falls back to
    // '#' for a stale/renamed route name rather than throwing, since this feeds public pages.
    protected function resolvedUrl(): Attribute
    {
        return Attribute::get(function () {
            return match ($this->link_type) {
                'route'    => Route::has($this->link_value) ? route($this->link_value) : '#',
                'cms_page' => route('pages', $this->link_value),
                'category' => route('packages.index', ['category' => $this->link_value]),
                default    => $this->link_value ?: '#',
            };
        });
    }

    // Mirrors resolvedUrl()'s per-type rules to decide whether this item represents the
    // page currently being viewed, for the navbar's "active" highlight.
    protected function isCurrent(): Attribute
    {
        return Attribute::get(function () {
            return match ($this->link_type) {
                'route'    => request()->routeIs($this->link_value),
                'category' => request()->routeIs('packages.index') && request()->get('category') === $this->link_value,
                'cms_page' => request()->routeIs('pages') && request()->route('slug') === $this->link_value,
                default    => false,
            };
        });
    }
}
