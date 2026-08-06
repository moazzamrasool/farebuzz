<?php

namespace App\Services\WhatsApp;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// AI layer for the WhatsApp lead-collection bot — conversationally gathers name, email,
// and requirement, one question at a time, then hands back to
// WhatsAppConversationOrchestrator for package matching + lead creation. Reuses the same
// config('services.ai.*') provider (Gemini / OpenAI-compatible) as App\Services\AiPackageService,
// but is kept as its own class since the prompt/schema are unrelated — see that class's own
// header comment for why it's treated as the single owner of its integration.
class WhatsAppConversationAiService
{
    private const REQUIRED_KEYS = ['reply', 'extracted', 'done'];

    // $history: list of ['role' => 'user'|'assistant', 'content' => string].
    // $collected: current known fields, e.g. ['name' => null, 'email' => null, 'requirement' => null].
    public function converse(array $history, array $collected, string $incomingMessage): array
    {
        $prompt = $this->buildPrompt($history, $collected, $incomingMessage);

        $data = $this->parseJson($this->callProvider($prompt));

        if ($data === null) {
            $data = $this->parseJson($this->callProvider($prompt, strict: true));
        }

        if ($data === null) {
            throw new \RuntimeException('WhatsApp bot AI returned a response we could not read.');
        }

        return $data;
    }

    private function callProvider(string $prompt, bool $strict = false): string
    {
        if ($strict) {
            $prompt .= "\n\nIMPORTANT: Your previous response was not valid JSON. Respond with ONLY the raw JSON object — no markdown code fences, no commentary, no leading or trailing text.";
        }

        $started = microtime(true);

        try {
            $text = match (config('services.ai.provider', 'gemini')) {
                'openai_compatible' => $this->callOpenAiCompatible($prompt),
                default => $this->callGemini($prompt),
            };
        } catch (\Throwable $e) {
            $this->logAttempt($started, false);
            throw $e;
        }

        $this->logAttempt($started, true);

        return $text;
    }

    private function callGemini(string $prompt): string
    {
        $key = config('services.ai.gemini.key');
        $model = config('services.ai.gemini.model');
        $baseUrl = rtrim(config('services.ai.gemini.base_url'), '/');

        if (!$key) {
            throw new \RuntimeException('WhatsApp bot AI is not configured — a Gemini API key is missing.');
        }

        $response = Http::timeout(20)
            ->withOptions(['query' => ['key' => $key]])
            ->post("{$baseUrl}/models/{$model}:generateContent", [
                'contents' => [
                    ['parts' => [['text' => $prompt]]],
                ],
                'generationConfig' => [
                    'temperature' => 0.4,
                    'response_mime_type' => 'application/json',
                ],
            ]);

        $this->assertOk($response, 'Gemini');

        $text = $response->json('candidates.0.content.parts.0.text');

        if (!$text) {
            throw new \RuntimeException('WhatsApp bot AI returned an empty response.');
        }

        return $text;
    }

    private function callOpenAiCompatible(string $prompt): string
    {
        $key = config('services.ai.openai_compatible.key');
        $model = config('services.ai.openai_compatible.model');
        $baseUrl = rtrim(config('services.ai.openai_compatible.base_url'), '/');

        if (!$key) {
            throw new \RuntimeException('WhatsApp bot AI is not configured — an API key is missing.');
        }

        $response = Http::timeout(20)
            ->withToken($key)
            ->post("{$baseUrl}/chat/completions", [
                'model' => $model,
                'messages' => [['role' => 'user', 'content' => $prompt]],
                'temperature' => 0.4,
                'response_format' => ['type' => 'json_object'],
            ]);

        $this->assertOk($response, 'AI provider');

        $text = $response->json('choices.0.message.content');

        if (!$text) {
            throw new \RuntimeException('WhatsApp bot AI returned an empty response.');
        }

        return $text;
    }

    private function assertOk(\Illuminate\Http\Client\Response $response, string $providerLabel): void
    {
        if ($response->status() === 429) {
            throw new \RuntimeException("{$providerLabel}'s quota has been used up for now.");
        }

        if ($response->failed()) {
            throw new \RuntimeException("Could not reach {$providerLabel} right now.");
        }
    }

    private function parseJson(string $raw): ?array
    {
        $cleaned = trim($raw);
        $cleaned = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $cleaned);
        $cleaned = trim($cleaned);

        $data = json_decode($cleaned, true);

        if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        foreach (self::REQUIRED_KEYS as $key) {
            if (!array_key_exists($key, $data)) {
                return null;
            }
        }

        if (!is_array($data['extracted'] ?? null)) {
            return null;
        }

        return $data;
    }

    private function buildPrompt(array $history, array $collected, string $incomingMessage): string
    {
        $schema = <<<'SCHEMA'
{
  "reply": "string — your next message back to the customer on WhatsApp",
  "extracted": {
    "name": "string or null — only if newly learned or corrected this turn",
    "email": "string or null — only if newly learned or corrected this turn",
    "requirement": "string or null — a short summary of what trip/package they want, only if newly learned or corrected this turn"
  },
  "done": "boolean — true once name, email, and requirement are all known"
}
SCHEMA;

        $historyText = collect($history)
            ->map(fn (array $m) => ($m['role'] === 'user' ? 'Customer' : 'Bot').': '.$m['content'])
            ->implode("\n");

        $knownText = collect($collected)
            ->map(fn ($v, $k) => $k.': '.($v ?: '(not yet known)'))
            ->implode("\n");

        return <<<PROMPT
You are a friendly travel-agency WhatsApp assistant. Your only job is to collect the
customer's name, email, and travel requirement (destination/trip they're interested in)
through natural conversation, asking ONE question at a time — never ask for more than one
missing field in a single reply. Once all three are known, thank them and let them know an
agent will follow up shortly with package recommendations; set "done" to true.

Fields known so far:
{$knownText}

Conversation so far:
{$historyText}

Customer's latest message: {$incomingMessage}

Respond with ONLY a single JSON object matching exactly this shape (no markdown fences,
no explanation before or after it):

{$schema}

Rules:
- Keep "reply" short and conversational, suitable for a WhatsApp message.
- Only put a value in "extracted" for a field you learned or that the customer corrected
  this turn — leave already-known fields as null in "extracted" to avoid overwriting them.
- Never invent or guess a value for name/email/requirement.
PROMPT;
    }

    private function logAttempt(float $startedAt, bool $ok): void
    {
        Log::info('whatsapp_bot_ai_converse', [
            'provider' => config('services.ai.provider', 'gemini'),
            'ok' => $ok,
            'duration_ms' => (int) ((microtime(true) - $startedAt) * 1000),
        ]);
    }
}
