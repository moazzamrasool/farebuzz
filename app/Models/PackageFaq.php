<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageFaq extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'holiday_package_id',
        'question',
        'answer',
        'sort_order',
    ];

    public function holidayPackage(): BelongsTo
    {
        return $this->belongsTo(HolidayPackage::class);
    }
}
