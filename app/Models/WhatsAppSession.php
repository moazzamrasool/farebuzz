<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Tracks one in-progress (or completed) WhatsApp lead-collection conversation for a
// single customer phone number, within a single tenant. See
// App\Services\WhatsApp\WhatsAppConversationOrchestrator.
class WhatsAppSession extends Model
{
    use BelongsToTenant;

    // Eloquent's snake_case inference would otherwise split "WhatsApp" into
    // "whats_app", producing "whats_app_sessions".
    protected $table = 'whatsapp_sessions';

    protected $fillable = [
        'unique_id',
        'wa_phone',
        'customer_name',
        'customer_email',
        'requirement',
        'conversation_history',
        'matched_holiday_package_id',
        'package_enquiry_id',
        'status',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'conversation_history' => 'array',
            'last_message_at' => 'datetime',
        ];
    }

    public function matchedHolidayPackage(): BelongsTo
    {
        return $this->belongsTo(HolidayPackage::class, 'matched_holiday_package_id');
    }

    public function packageEnquiry(): BelongsTo
    {
        return $this->belongsTo(PackageEnquiry::class);
    }

    public function collectedFields(): array
    {
        return [
            'name' => $this->customer_name,
            'email' => $this->customer_email,
            'requirement' => $this->requirement,
        ];
    }
}
