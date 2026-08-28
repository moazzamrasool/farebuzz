<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageItineraryImage extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'package_itinerary_id',
        'image',
        'caption',
        'alt_text',
        'sort_order',
    ];

    public function itinerary(): BelongsTo
    {
        return $this->belongsTo(PackageItinerary::class, 'package_itinerary_id');
    }
}
