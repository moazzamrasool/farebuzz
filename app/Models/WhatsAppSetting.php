<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

// One row per tenant — a company's own WhatsApp Business Cloud API credentials.
// See App\Services\WhatsApp\WhatsAppCloudApiClient and WhatsAppWebhookController.
class WhatsAppSetting extends Model
{
    use BelongsToTenant;

    // Eloquent's snake_case inference would otherwise split "WhatsApp" into
    // "whats_app", producing "whats_app_settings".
    protected $table = 'whatsapp_settings';

    protected $fillable = [
        'unique_id',
        'phone_number_id',
        'access_token',
        'business_account_id',
        'enabled',
    ];

    protected function casts(): array
    {
        return [
            'access_token' => 'encrypted',
            'enabled' => 'boolean',
        ];
    }

    // Mirrors AiPackageSetting::isEnabledForCurrentTenant() — always runs behind the
    // admin guard's tenant scope, so firstOrCreate naturally lands on that tenant's
    // single row (or creates an empty, disabled one).
    public static function forCurrentTenant(): self
    {
        return static::query()->firstOrCreate([], ['enabled' => false]);
    }

    // Called from the unauthenticated webhook — TenantScope is a no-op without an
    // admin session, so this explicitly bypasses it and filters by the phone number
    // Meta sent in the payload instead of the (nonexistent) logged-in admin.
    public static function resolveByPhoneNumberId(string $phoneNumberId): ?self
    {
        return static::withoutGlobalScopes()
            ->where('phone_number_id', $phoneNumberId)
            ->where('enabled', true)
            ->first();
    }
}
