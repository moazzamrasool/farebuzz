<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HolidayPackageCustomFeature extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'holiday_package_custom_features';

    protected $fillable = [
        'holiday_package_id',
        'type',
        'title',
        'sort_order',
    ];

    public function holidayPackage(): BelongsTo
    {
        return $this->belongsTo(HolidayPackage::class);
    }
}
