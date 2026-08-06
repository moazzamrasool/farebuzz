<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Package extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'travel_category_id',
        'name',
        'slug',
        'type',
        'description',
        'meta',
        'image',
        'banner_image',
        'status',
        'sort_order',
    ];

    public function travelCategory(): BelongsTo
    {
        return $this->belongsTo(TravelCategory::class);
    }
}
