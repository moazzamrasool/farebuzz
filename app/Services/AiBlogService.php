<?php

namespace App\Services;

use App\Exceptions\AiGenerationException;
use App\Models\Blog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Isolated AI-drafting integration for the Blog editor — mirrors App\Services\AiPackageService's
// provider-calling shape (config('services.ai.*')) but is kept as its own standalone class
// rather than sharing code with it, so a change here can never regress the package generator.
// The AI only ever returns a JSON draft (plus, best-effort, a generated cover image) that the
// admin reviews/edits in the form — nothing here writes to the database.
class AiBlogService
{
    private const REQUIRED_KEYS = [
        'title', 'slug', 'category', 'excerpt', 'content_html',
        'meta_title', 'meta_description', 'meta_keywords', 'focus_keyword',
        'tags', 'og_title', 'og_description', 'reading_time',
    ];

    // $inputs: topic, category, tone.
    public function generate(array $inputs): array
    {
        $prompt = $this->buildPrompt($inputs);

        // Some OpenAI-compatible "reasoning" models (e.g. Groq's gpt-oss-*) intermittently
        // truncate their own JSON output before it closes, which the provider itself reports
        // as a 400 (not just a parse failure) — so the first attempt is allowed to fail with
        // an AiGenerationException too, and gets exactly one retry just like a bad-JSON result.
        try {
            $data = $this->parseJson($this->callProvider($prompt));
        } catch (AiGenerationException) {
            $data = null;
        }

        if ($data === null) {
            // A failure on this second, stricter attempt is the more informative error to
            // show the admin, so it's allowed to propagate instead of being swallowed too.
            $data = $this->parseJson($this->callProvider($prompt, strict: true));
        }

        if ($data === null) {
            throw new AiGenerationException('The AI returned a response we could not read. Please try again.');
        }

        if (!array_key_exists((string) $data['category'], Blog::CATEGORIES)) {
            $data['category'] = $inputs['category'] ?? 'general';
        }

        return $data;
    }

    // Best-effort cover image via Gemini's image-generation model — returns raw base64 PNG
    // data (no data: prefix) or null on ANY failure. Deliberately never throws: a missing/
    // unavailable image model should never block the text draft the admin actually needs.
    public function generateImage(string $title, string $excerpt): ?string
    {
        if (config('services.ai.provider', 'gemini') !== 'gemini') {
            return null;
        }

        $key = config('services.ai.gemini.key');
        $model = config('services.ai.gemini.image_model');
        $baseUrl = rtrim(config('services.ai.gemini.base_url'), '/');

        if (!$key || !$model) {
            return null;
        }

        $prompt = "A realistic, high-quality travel/tourism photograph suitable as a blog cover image for an article titled \"{$title}\". {$excerpt} Landscape orientation, no text or watermarks overlaid on the image.";

        try {
            $response = Http::timeout(30)
                ->withOptions(['query' => ['key' => $key]])
                ->post("{$baseUrl}/models/{$model}:generateContent", [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]],
                    ],
                    'generationConfig' => [
                        'responseModalities' => ['IMAGE'],
                    ],
                ]);

            if ($response->failed()) {
                Log::warning('ai_blog_generate_image_failed', [
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 2000),
                ]);

                return null;
            }

            $parts = $response->json('candidates.0.content.parts', []);

            foreach ($parts as $part) {
                if (!empty($part['inlineData']['data'])) {
                    return $part['inlineData']['data'];
                }
            }

            return null;
        } catch (\Throwable $e) {
            Log::warning('ai_blog_generate_image_exception', ['message' => $e->getMessage()]);

            return null;
        }
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

        try {
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
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::warning('ai_blog_generate_connection_failed', ['provider' => 'gemini', 'message' => $e->getMessage()]);
            throw new AiGenerationException('Could not connect to Gemini — check this server\'s internet access and try again.');
        }

        $this->assertOk($response, 'Gemini');

        $text = $response->json('candidates.0.content.parts.0.text');

        if (!$text) {
            $blockReason = $response->json('promptFeedback.blockReason');
            throw new AiGenerationException($blockReason
                ? "The AI declined to generate this blog post ({$blockReason})."
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

        try {
            $response = Http::timeout(25)
                ->withToken($key)
                ->post("{$baseUrl}/chat/completions", [
                    'model' => $model,
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'temperature' => 0.7,
                    // Generous headroom: "reasoning" models (e.g. Groq's gpt-oss-*) spend part
                    // of this budget on a hidden reasoning trace before the actual JSON content,
                    // and a tight default here is what was truncating that JSON mid-object.
                    'max_tokens' => 8000,
                    'response_format' => ['type' => 'json_object'],
                ]);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::warning('ai_blog_generate_connection_failed', ['provider' => 'openai_compatible', 'message' => $e->getMessage()]);
            throw new AiGenerationException('Could not connect to the AI provider — check this server\'s internet access and try again.');
        }

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
            throw new AiGenerationException("{$providerLabel}'s free quota has been used up for now. Try again later.");
        }

        if ($response->failed()) {
            Log::warning('ai_blog_generate_failed_response', [
                'provider' => $providerLabel,
                'status' => $response->status(),
                'body' => substr($response->body(), 0, 2000),
            ]);
            throw new AiGenerationException("Could not reach {$providerLabel} right now ({$response->status()}). Please try again in a moment.");
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

        return $data;
    }

    private function buildPrompt(array $inputs): string
    {
        $topic = $inputs['topic'] ?? '';
        $category = $inputs['category'] ?? 'general';
        $categoryLabel = Blog::CATEGORIES[$category] ?? 'General';
        $tone = $inputs['tone'] ?? 'informative';
        $categoryKeys = implode(', ', array_keys(Blog::CATEGORIES));

        $schema = <<<'SCHEMA'
{
  "title": "string",
  "slug": "string (lowercase, hyphenated)",
  "category": "string, one of the allowed category keys",
  "excerpt": "string, 140-160 characters, used as the card preview summary",
  "content_html": "string, HTML using only <h2>, <h3>, <p>, <strong>, <em>, <ul>, <li>, <blockquote> tags",
  "meta_title": "string, ideally 50-60 characters",
  "meta_description": "string, ideally 150-160 characters",
  "meta_keywords": "comma-separated string",
  "focus_keyword": "string",
  "tags": "comma-separated string",
  "og_title": "string",
  "og_description": "string",
  "reading_time": integer, estimated minutes to read the article
}
SCHEMA;

        return <<<PROMPT
You are drafting a blog post for a travel agency's admin panel. The admin will review
and edit every field before publishing, so give a strong, realistic, well-structured
first draft rather than placeholders.

Topic / brief from the admin: {$topic}
Suggested category: {$categoryLabel}
Tone: {$tone}

Respond with ONLY a single JSON object matching exactly this shape (no markdown fences,
no explanation before or after it):

{$schema}

Rules:
- "category" must be exactly one of: {$categoryKeys}.
- "content_html" should be a complete, well-organised article of roughly 600-900 words,
  broken into sections with <h2>/<h3> headings — not one long block of text.
- Keep all HTML plain and safe — no scripts, styles, iframes, or external images.
- Do not invent fake statistics, prices, named hotels, or named tour operators.
PROMPT;
    }

    private function logAttempt(float $startedAt, bool $ok): void
    {
        Log::info('ai_blog_generate', [
            'provider' => config('services.ai.provider', 'gemini'),
            'ok' => $ok,
            'duration_ms' => (int) ((microtime(true) - $startedAt) * 1000),
        ]);
    }
}
