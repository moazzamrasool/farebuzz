<?php

namespace App\Contracts;

interface WhatsAppSenderInterface
{
    // Returns the URL the browser/agent should be sent to in order to deliver the
    // message. Phase 1 (ClickToChatWhatsAppSender) returns a wa.me deep link that opens
    // WhatsApp Web/app with the message pre-filled. A future WhatsApp Business API
    // implementation would instead call the provider's API here and can still return
    // a URL (e.g. a confirmation page) to keep the controller contract unchanged.
    public function buildLink(string $phone, string $message): string;
}
