<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

// One row per tenant — lets a company owner kill the "AI Generate" feature (e.g. if
// the free Gemini quota runs out) without touching .env. See App\Services\AiPackageService.
class AiPackageSetting extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'unique_id',
        'enabled',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
        ];
    }

    // Super Admin has no unique_id (global), so it never has a settings row of its
    // own — this call site always runs behind the admin guard's tenant scope, where
    // firstOrCreate naturally lands on that tenant's single row (or creates it,
    // defaulting to enabled).
    public static function isEnabledForCurrentTenant(): bool
    {
        // Explicit default in $values (not just the DB column default) so a
        // freshly-created row's in-memory `enabled` isn't null before its first reload.
        return static::query()->firstOrCreate([], ['enabled' => true])->enabled;
    }
}
