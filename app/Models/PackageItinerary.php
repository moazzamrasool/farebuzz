<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageItinerary extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'holiday_package_id',
        'day_number',
        'title',
        'route_summary',
        'detail',
        'bullet_points',
        'meal_tags',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'bullet_points' => 'array',
            'meal_tags'     => 'array',
        ];
    }

    public function holidayPackage(): BelongsTo
    {
        return $this->belongsTo(HolidayPackage::class);
    }
}
