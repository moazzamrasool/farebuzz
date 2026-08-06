<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PackageFeature extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'title',
        'icon',
        'type',
        'status',
        'sort_order',
    ];

    public function scopeInclusions(Builder $query): Builder
    {
        return $query->where('type', 'inclusion');
    }

    public function scopeExclusions(Builder $query): Builder
    {
        return $query->where('type', 'exclusion');
    }

    public function holidayPackages(): BelongsToMany
    {
        return $this->belongsToMany(HolidayPackage::class, 'holiday_package_feature');
    }
}
