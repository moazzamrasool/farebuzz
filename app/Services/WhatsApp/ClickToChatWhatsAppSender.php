<?php

namespace App\Services\WhatsApp;

use App\Contracts\WhatsAppSenderInterface;

class ClickToChatWhatsAppSender implements WhatsAppSenderInterface
{
    public function buildLink(string $phone, string $message): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        return 'https://wa.me/'.$digits.'?text='.rawurlencode($message);
    }
}
