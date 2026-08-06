<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppSetting;
use App\Services\WhatsApp\WhatsAppConversationOrchestrator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

// Public, unauthenticated endpoints Meta calls directly — see routes/whatsapp.php and
// the CSRF exemption in bootstrap/app.php. There is no admin session here, so nothing
// downstream (WhatsAppSetting/WhatsAppSession/PackageEnquiry) can rely on tenant global
// scopes; tenant is resolved explicitly from the phone_number_id Meta sends.
class WhatsAppWebhookController extends Controller
{
    // One shared Meta Developer App handles verification for every tenant's WhatsApp
    // number, so the verify token is a single global value, unlike the per-tenant
    // access_token/phone_number_id stored in WhatsAppSetting.
    public function verify(Request $request): Response
    {
        $mode = $request->query('hub_mode', $request->query('hub.mode'));
        $token = $request->query('hub_verify_token', $request->query('hub.verify_token'));
        $challenge = $request->query('hub_challenge', $request->query('hub.challenge'));

        $expected = config('services.whatsapp_cloud.verify_token');

        if ($mode === 'subscribe' && $expected && hash_equals($expected, (string) $token)) {
            return response((string) $challenge, 200);
        }

        return response('Forbidden', 403);
    }

    public function receive(Request $request, WhatsAppConversationOrchestrator $orchestrator): Response
    {
        if (!$this->hasValidSignature($request)) {
            Log::warning('whatsapp_webhook_bad_signature');

            return response('Forbidden', 403);
        }

        $value = $request->input('entry.0.changes.0.value', []);
        $message = $value['messages'][0] ?? null;

        // Meta also posts delivery/read status callbacks (no "messages" key) to the same
        // URL — acknowledge with 200 so it doesn't retry, but there's nothing to process.
        if (!$message || ($message['type'] ?? null) !== 'text') {
            return response('EVENT_RECEIVED', 200);
        }

        $phoneNumberId = $value['metadata']['phone_number_id'] ?? null;
        $from = $message['from'] ?? null;
        $text = $message['text']['body'] ?? null;

        if (!$phoneNumberId || !$from || !$text) {
            return response('EVENT_RECEIVED', 200);
        }

        $setting = WhatsAppSetting::resolveByPhoneNumberId($phoneNumberId);

        if (!$setting) {
            Log::warning('whatsapp_webhook_unknown_phone_number_id', ['phone_number_id' => $phoneNumberId]);

            return response('EVENT_RECEIVED', 200);
        }

        $orchestrator->handle($setting, $from, $text);

        return response('EVENT_RECEIVED', 200);
    }

    private function hasValidSignature(Request $request): bool
    {
        $secret = config('services.whatsapp_cloud.app_secret');

        // No secret configured yet (fresh setup) — don't hard-fail the whole webhook,
        // but this should be filled in before going live.
        if (!$secret) {
            return true;
        }

        $header = $request->header('X-Hub-Signature-256', '');

        if (!str_starts_with($header, 'sha256=')) {
            return false;
        }

        $expected = hash_hmac('sha256', $request->getContent(), $secret);

        return hash_equals($expected, substr($header, 7));
    }
}
