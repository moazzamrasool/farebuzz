<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackagePhoto extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'holiday_package_id',
        'path',
        'alt_text',
        'is_cover',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_cover' => 'boolean',
        ];
    }

    public function holidayPackage(): BelongsTo
    {
        return $this->belongsTo(HolidayPackage::class);
    }
}
