<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageReview extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'holiday_package_id',
        'reviewer_name',
        'rating',
        'hotels_rating',
        'sightseeing_rating',
        'food_rating',
        'value_rating',
        'comment',
        'review_date',
        'verified',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'review_date' => 'date',
            'verified'    => 'boolean',
        ];
    }

    public function holidayPackage(): BelongsTo
    {
        return $this->belongsTo(HolidayPackage::class);
    }
}
