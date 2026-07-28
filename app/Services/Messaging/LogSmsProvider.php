<?php

namespace App\Services\Messaging;

use App\Contracts\SmsProviderInterface;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class LogSmsProvider implements SmsProviderInterface
{
    public function send(string $to, string $message, array $options = []): array
    {
        Log::info('SMS stub send', ['to' => $to, 'message' => $message, 'options' => $options]);

        return [
            'success' => true,
            'provider' => 'log',
            'message_id' => 'sms_'.uniqid(),
            'configured' => (bool) Setting::get('sms_api_key', null, 'integrations'),
        ];
    }
}
