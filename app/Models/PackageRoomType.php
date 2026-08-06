<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageRoomType extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'holiday_package_id',
        'name',
        'price',
        'discounted_price',
        'sort_order',
    ];

    public function holidayPackage(): BelongsTo
    {
        return $this->belongsTo(HolidayPackage::class);
    }

    // The actual per-person price charged for this room — falls back to the
    // original price when no discount is set, so callers never need a null check.
    protected function sellPrice(): Attribute
    {
        return Attribute::get(fn () => (float) ($this->discounted_price ?? $this->price));
    }

    protected function savingsPercent(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->discounted_price || !$this->price || $this->price <= 0 || $this->discounted_price >= $this->price) {
                return null;
            }

            return (int) round((($this->price - $this->discounted_price) / $this->price) * 100);
        });
    }
}
