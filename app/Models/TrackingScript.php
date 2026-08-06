<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\ForSiteTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// One row per tenant — raw header/body/footer scripts (GA4, GTM, Facebook Pixel, ...)
// injected into every frontend page. Company-owner-only, see TrackingScriptController.
class TrackingScript extends Model
{
    use BelongsToTenant, ForSiteTenant;

    protected $fillable = [
        'unique_id',
        'header_script',
        'header_enabled',
        'body_script',
        'body_enabled',
        'footer_script',
        'footer_enabled',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'header_enabled' => 'boolean',
            'body_enabled'   => 'boolean',
            'footer_enabled' => 'boolean',
        ];
    }

    // Mirrors WhatsAppSetting::forCurrentTenant().
    public static function forCurrentTenant(): self
    {
        return static::query()->firstOrCreate([]);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(Admin\Admin::class, 'updated_by');
    }
}
