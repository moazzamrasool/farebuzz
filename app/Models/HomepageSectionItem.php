<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\ForSiteTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomepageSectionItem extends Model
{
    use BelongsToTenant, ForSiteTenant;

    protected $fillable = [
        'homepage_section_id',
        'coupon_id',
        'group_key',
        'image',
        'title',
        'subtitle',
        'description',
        'label',
        'link',
        'button_label',
        'price',
        'old_price',
        'rating',
        'review_count',
        'meta',
        'sort_order',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'price' => 'decimal:2',
            'old_price' => 'decimal:2',
            'rating' => 'decimal:1',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(HomepageSection::class, 'homepage_section_id');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
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
