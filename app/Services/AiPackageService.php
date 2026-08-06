<?php

namespace App\Services;

use App\Exceptions\AiGenerationException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Isolated AI-drafting integration for the Holiday Package builder — the only class
// that talks to an external AI provider. Provider is entirely config-driven
// (config('services.ai.*')); swapping Gemini for a Groq/OpenRouter key is a .env
// change only, no code here changes. The AI only ever returns a JSON draft that the
// admin reviews/edits in the form — nothing here writes to the database.
class AiPackageService
{
    private const REQUIRED_KEYS = [
        'title', 'overview_html', 'itinerary', 'inclusions', 'exclusions',
        'activities', 'faqs', 'hotel_category', 'meals', 'language', 'price_estimate',
    ];

    // $inputs: destination, nights, theme, budget, traveller_type, extra_instructions.
    public function generate(array $inputs): array
    {
        $prompt = $this->buildPrompt($inputs);

        $data = $this->parseJson($this->callProvider($prompt));

        if ($data === null) {
            // One retry with a sharper instruction — the most common failure mode is
            // the model wrapping the JSON in prose or a markdown fence despite being
            // told not to.
            $data = $this->parseJson($this->callProvider($prompt, strict: true));
        }

        if ($data === null) {
            throw new AiGenerationException('The AI returned a response we could not read. Please try again.');
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
        } catch (AiGenerationException $e) {
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
            throw new AiGenerationException('AI generation is not configured yet — a Gemini API key is missing.');
        }

        $response = Http::timeout(25)
            ->withOptions(['query' => ['key' => $key]])
            ->post("{$baseUrl}/models/{$model}:generateContent", [
                'contents' => [
                    ['parts' => [['text' => $prompt]]],
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'response_mime_type' => 'application/json',
                ],
            ]);

        $this->assertOk($response, 'Gemini');

        $text = $response->json('candidates.0.content.parts.0.text');

        if (!$text) {
            $blockReason = $response->json('promptFeedback.blockReason');
            throw new AiGenerationException($blockReason
                ? "The AI declined to generate this package ({$blockReason})."
                : 'The AI returned an empty response. Please try again.');
        }

        return $text;
    }

    private function callOpenAiCompatible(string $prompt): string
    {
        $key = config('services.ai.openai_compatible.key');
        $model = config('services.ai.openai_compatible.model');
        $baseUrl = rtrim(config('services.ai.openai_compatible.base_url'), '/');

        if (!$key) {
            throw new AiGenerationException('AI generation is not configured yet — an API key is missing.');
        }

        $response = Http::timeout(25)
            ->withToken($key)
            ->post("{$baseUrl}/chat/completions", [
                'model' => $model,
                'messages' => [['role' => 'user', 'content' => $prompt]],
                'temperature' => 0.7,
                'response_format' => ['type' => 'json_object'],
            ]);

        $this->assertOk($response, 'AI provider');

        $text = $response->json('choices.0.message.content');

        if (!$text) {
            throw new AiGenerationException('The AI returned an empty response. Please try again.');
        }

        return $text;
    }

    private function assertOk(\Illuminate\Http\Client\Response $response, string $providerLabel): void
    {
        if ($response->status() === 429) {
            throw new AiGenerationException("{$providerLabel}'s free quota has been used up for now. Try again later, or disable AI Generate in settings.");
        }

        if ($response->failed()) {
            throw new AiGenerationException("Could not reach {$providerLabel} right now. Please try again in a moment.");
        }
    }

    private function parseJson(string $raw): ?array
    {
        $cleaned = trim($raw);
        // Strip a ```json ... ``` or ``` ... ``` fence if the model wrapped its output anyway.
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

        return $data;
    }

    private function buildPrompt(array $inputs): string
    {
        $destination = $inputs['destination'] ?? '';
        $nights = (int) ($inputs['nights'] ?? 1);
        $days = $nights + 1;
        $theme = $inputs['theme'] ?? 'general leisure';
        $budget = $inputs['budget'] ?? 'mid-range';
        $travellerType = $inputs['traveller_type'] ?? 'couple';
        $extra = trim((string) ($inputs['extra_instructions'] ?? ''));

        $schema = <<<'SCHEMA'
{
  "title": "string",
  "slug": "string (lowercase, hyphenated)",
  "short_description": "string, one or two sentences",
  "overview_html": "string, basic HTML using only <p>, <strong>, <ul>, <li> tags",
  "itinerary": [
    {
      "day_number": 1,
      "title": "string",
      "route_summary": "string, short one-liner",
      "detail_html": "string, basic HTML using only <p>, <strong>, <ul>, <li> tags",
      "bullet_points": ["string", "..."],
      "meal_tags": ["breakfast", "lunch", "dinner"]
    }
  ],
  "inclusions": ["string", "..."],
  "exclusions": ["string", "..."],
  "activities": ["string", "..."],
  "faqs": [{ "question": "string", "answer": "string" }],
  "hotel_category": "string, e.g. 4 Star Deluxe",
  "meals": "string, e.g. Daily Breakfast",
  "language": "string, e.g. English / Hindi",
  "price_estimate": { "price": 0, "discounted_price": 0, "currency": "INR", "note": "AI estimate — confirm before publishing" }
}
SCHEMA;

        $extraLine = $extra !== '' ? "Extra instructions from the admin: {$extra}\n" : '';

        return <<<PROMPT
You are drafting a holiday tour package for a travel agency's admin panel. The admin
will review and edit every field before publishing, so give a strong, realistic first
draft rather than placeholders.

Trip details:
- Destination: {$destination}
- Duration: {$nights} nights, {$days} days
- Theme / category: {$theme}
- Budget level: {$budget}
- Traveller type: {$travellerType}
{$extraLine}
Respond with ONLY a single JSON object matching exactly this shape (no markdown fences,
no explanation before or after it):

{$schema}

Rules:
- "itinerary" must contain exactly {$days} entries, one per day, with "day_number" from 1 to {$days} in order.
- Keep "detail_html" and "overview_html" to plain, safe HTML — no scripts, no styles, no external images.
- "price_estimate" values are a rough per-person estimate in INR appropriate for the given budget level — always label it as an estimate.
- Do not invent named hotels or named tour operators; keep "hotel_category" generic (e.g. "3 Star", "4 Star Deluxe", "5 Star Luxury Resort").
PROMPT;
    }

    private function logAttempt(float $startedAt, bool $ok): void
    {
        Log::info('ai_package_generate', [
            'provider' => config('services.ai.provider', 'gemini'),
            'ok' => $ok,
            'duration_ms' => (int) ((microtime(true) - $startedAt) * 1000),
        ]);
    }
}
