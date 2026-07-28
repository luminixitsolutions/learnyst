<?php

namespace App\Services\Messaging;

use App\Contracts\WhatsAppProviderInterface;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class LogWhatsAppProvider implements WhatsAppProviderInterface
{
    public function send(string $to, string $message, array $options = []): array
    {
        Log::info('WhatsApp stub send', ['to' => $to, 'message' => $message, 'options' => $options]);

        return [
            'success' => true,
            'provider' => 'log',
            'message_id' => 'wa_'.uniqid(),
            'configured' => (bool) Setting::get('whatsapp_api_key', null, 'integrations'),
        ];
    }
}
