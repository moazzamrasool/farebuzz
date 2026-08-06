<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsAppSetting;
use Illuminate\Support\Facades\Log;
use Netflie\WhatsAppCloudApi\WhatsAppCloudApi;

// Thin wrapper around netflie/whatsapp-cloud-api for the AI lead-collection bot. Built
// per-call from a tenant's own WhatsAppSetting row (credentials differ per tenant), so
// this is deliberately not a container singleton — see App\Contracts\WhatsAppSenderInterface
// for the separate, unrelated admin-composed "click to chat" outbound flow.
class WhatsAppCloudApiClient
{
    public function sendText(WhatsAppSetting $setting, string $to, string $message): void
    {
        $client = new WhatsAppCloudApi([
            'from_phone_number_id' => $setting->phone_number_id,
            'access_token' => $setting->access_token,
            'business_id' => $setting->business_account_id,
        ]);

        try {
            // Client::sendMessage() throws Response\ResponseException itself on any
            // Graph API error — nothing further to check on a successful return.
            $client->sendTextMessage($to, $message);
        } catch (\Throwable $e) {
            // A failed outbound send shouldn't turn a webhook delivery into a 500 —
            // Meta would just retry the same inbound payload. Log and move on.
            Log::warning('whatsapp_bot_send_exception', [
                'unique_id' => $setting->unique_id,
                'to' => $to,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
