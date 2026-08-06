<?php

namespace App\Services\WhatsApp;

use App\Enums\LeadStatus;
use App\Models\PackageEnquiry;
use App\Models\WhatsAppSession;
use App\Models\WhatsAppSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

// Ties one inbound WhatsApp message to: session lookup/update, the AI conversation turn,
// the outbound reply, and — once all fields are collected — package matching + lead
// creation. Called from WhatsAppWebhookController::receive().
class WhatsAppConversationOrchestrator
{
    public function __construct(
        private readonly WhatsAppConversationAiService $ai,
        private readonly WhatsAppCloudApiClient $client,
        private readonly WhatsAppPackageMatcher $matcher,
    ) {
    }

    public function handle(WhatsAppSetting $setting, string $fromPhone, string $messageText): void
    {
        $limiterKey = "whatsapp-bot:{$setting->unique_id}:{$fromPhone}";
        $maxPerHour = (int) config('services.whatsapp_cloud.rate_limit_per_hour', 30);

        if (RateLimiter::tooManyAttempts($limiterKey, $maxPerHour)) {
            Log::warning('whatsapp_bot_rate_limited', ['unique_id' => $setting->unique_id, 'phone' => $fromPhone]);

            return;
        }

        RateLimiter::hit($limiterKey, 3600);

        // TenantScope is a no-op without an authenticated admin, so every query/create
        // below filters/sets unique_id explicitly instead of relying on it.
        $session = WhatsAppSession::withoutGlobalScopes()->firstOrCreate(
            ['unique_id' => $setting->unique_id, 'wa_phone' => $fromPhone, 'status' => 'in_progress'],
            ['conversation_history' => []]
        );

        $history = $session->conversation_history ?? [];
        $history[] = ['role' => 'user', 'content' => $messageText];

        try {
            $result = $this->ai->converse($history, $session->collectedFields(), $messageText);
        } catch (\Throwable $e) {
            Log::warning('whatsapp_bot_ai_failed', [
                'unique_id' => $setting->unique_id,
                'phone' => $fromPhone,
                'error' => $e->getMessage(),
            ]);
            // Same fallback the AI itself would use to ask a first question — keeps the
            // conversation alive instead of leaving the customer without a reply.
            $result = ['reply' => "Sorry, could you say that again? I'd love to help — what's your name?", 'extracted' => [], 'done' => false];
        }

        foreach (['name' => 'customer_name', 'email' => 'customer_email', 'requirement' => 'requirement'] as $extractedKey => $column) {
            if (!empty($result['extracted'][$extractedKey])) {
                $session->{$column} = $result['extracted'][$extractedKey];
            }
        }

        $history[] = ['role' => 'assistant', 'content' => $result['reply']];
        $session->conversation_history = $history;
        $session->last_message_at = now();
        $session->save();

        $this->client->sendText($setting, $fromPhone, $result['reply']);

        $ready = $session->customer_name && $session->customer_email && $session->requirement;

        if (($result['done'] ?? false) && $ready) {
            $this->createLead($setting, $session, $fromPhone);
        }
    }

    private function createLead(WhatsAppSetting $setting, WhatsAppSession $session, string $fromPhone): void
    {
        $matched = $this->matcher->match($setting->unique_id, $session->requirement);

        $lead = PackageEnquiry::create([
            'unique_id' => $setting->unique_id,
            'holiday_package_id' => $matched?->id,
            'name' => $session->customer_name,
            'email' => $session->customer_email,
            'phone' => $fromPhone,
            'message' => $session->requirement,
            'source' => 'whatsapp_bot',
            'status' => LeadStatus::New,
        ]);

        $session->status = 'completed';
        $session->matched_holiday_package_id = $matched?->id;
        $session->package_enquiry_id = $lead->id;
        $session->save();
    }
}
