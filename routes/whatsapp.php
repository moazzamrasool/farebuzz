<?php

/*
|--------------------------------------------------------------------------
| WhatsApp Cloud API Webhook Routes
|--------------------------------------------------------------------------
| Called directly by Meta — no admin session, CSRF-exempt (see bootstrap/app.php).
*/

use App\Http\Controllers\WhatsAppWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('webhook/whatsapp', [WhatsAppWebhookController::class, 'verify'])->name('whatsapp.webhook.verify');
Route::post('webhook/whatsapp', [WhatsAppWebhookController::class, 'receive'])->name('whatsapp.webhook.receive');
