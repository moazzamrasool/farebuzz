<?php

namespace App\Models;

use App\Models\Admin\Admin;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class LeadActivity extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'leadable_type',
        'leadable_id',
        'admin_id',
        'type',
        'channel',
        'description',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    public function leadable(): MorphTo
    {
        return $this->morphTo();
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
}
