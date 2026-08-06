<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotelReview extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'hotel_id',
        'reviewer_name',
        'rating',
        'location_rating',
        'cleanliness_rating',
        'service_rating',
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
            'verified' => 'boolean',
        ];
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }
}
