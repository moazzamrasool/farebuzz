<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // ── Social OAuth Providers ───────────────────────────────────────────
    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URI', '/auth/google/callback'),
    ],

    'facebook' => [
        'client_id'     => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect'      => env('FACEBOOK_REDIRECT_URI', '/auth/facebook/callback'),
    ],

    // AI Package Generate — see App\Services\AiPackageService. Default provider is
    // Google Gemini's free tier; swapping to Groq/OpenRouter (both OpenAI-compatible
    // chat/completions APIs) is a config-only change via AI_PROVIDER=openai_compatible.
    'ai' => [
        'provider' => env('AI_PROVIDER', 'gemini'),
        'rate_limit_per_hour' => (int) env('AI_RATE_LIMIT_PER_HOUR', 20),
        'gemini' => [
            'key' => env('GEMINI_API_KEY'),
            'model' => env('GEMINI_MODEL', 'gemini-flash-latest'),
            'image_model' => env('GEMINI_IMAGE_MODEL', 'gemini-2.5-flash-image'),
            'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
        ],
        'openai_compatible' => [
            'key' => env('AI_OPENAI_COMPATIBLE_KEY'),
            'model' => env('AI_OPENAI_COMPATIBLE_MODEL', 'llama-3.3-70b-versatile'),
            'base_url' => env('AI_OPENAI_COMPATIBLE_BASE_URL', 'https://api.groq.com/openai/v1'),
        ],
    ],

    // WhatsApp — see App\Contracts\WhatsAppSenderInterface, bound in AppServiceProvider.
    // Default is click-to-chat (wa.me links, no API/cost). Swapping to a real WhatsApp
    // Business API provider later is a matter of writing one class implementing the
    // interface and pointing WHATSAPP_DRIVER at it — no controller/view changes needed.
    'whatsapp' => [
        'driver' => env('WHATSAPP_DRIVER', \App\Services\WhatsApp\ClickToChatWhatsAppSender::class),
    ],

    // WhatsApp Cloud API — AI-driven lead-collection bot (inbound webhook + AI
    // conversation). See App\Http\Controllers\WhatsAppWebhookController and
    // App\Services\WhatsApp\*. Per-tenant credentials (access_token, phone_number_id,
    // business_account_id) live in the whatsapp_settings table via the admin settings
    // page, NOT here — only the single Meta Developer App's shared secrets are global.
    'whatsapp_cloud' => [
        'verify_token' => env('WHATSAPP_VERIFY_TOKEN'),
        'app_secret' => env('WHATSAPP_APP_SECRET'),
        'rate_limit_per_hour' => (int) env('WHATSAPP_BOT_RATE_LIMIT_PER_HOUR', 30),
    ],

    // PayU India — see App\Services\PayUService. Switching PAYU_MODE to "live" and
    // filling the live key/salt is the only change needed to go live; no code changes.
    'payu' => [
        'mode' => env('PAYU_MODE', 'test'),
        'test' => [
            'key'  => env('PAYU_TEST_KEY'),
            'salt' => env('PAYU_TEST_SALT'),
            'url'  => 'https://test.payu.in/_payment',
        ],
        'live' => [
            'key'  => env('PAYU_LIVE_KEY'),
            'salt' => env('PAYU_LIVE_SALT'),
            'url'  => 'https://secure.payu.in/_payment',
        ],
    ],

];
