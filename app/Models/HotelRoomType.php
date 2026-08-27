<?php

namespace App\Models;

use App\Enums\BedType;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotelRoomType extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'hotel_id',
        'name',
        'price',
        'discounted_price',
        'occupancy_adults',
        'occupancy_children',
        'bed_type',
        'size_sqft',
        'meal_plan',
        'refundable',
        'images',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'refundable' => 'boolean',
            'bed_type' => BedType::class,
        ];
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    // The actual per-night price charged for this room — falls back to the
    // original price when no discount is set, so callers never need a null check.
    protected function sellPrice(): Attribute
    {
        return Attribute::get(fn () => (float) ($this->discounted_price ?? $this->price));
    }

    protected function mealPlanLabel(): Attribute
    {
        return Attribute::get(fn () => match ($this->meal_plan) {
            'breakfast' => 'Breakfast Included',
            'breakfast_dinner' => 'Breakfast & Dinner Included',
            default => 'Room Only',
        });
    }
}
