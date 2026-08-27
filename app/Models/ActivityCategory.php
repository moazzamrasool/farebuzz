<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\ForSiteTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActivityCategory extends Model
{
    use HasFactory, BelongsToTenant, ForSiteTenant;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'status',
        'sort_order',
    ];

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }
}
