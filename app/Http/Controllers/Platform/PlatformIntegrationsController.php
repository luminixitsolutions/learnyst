<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\GoogleOAuthService;
use App\Services\RazorpayService;

class PlatformIntegrationsController extends Controller
{
    public function index(GoogleOAuthService $google, RazorpayService $razorpay)
    {
        $emailConfigured = filled(Setting::get('smtp_host', '', 'email'))
            && filled(Setting::get('mail_from_address', '', 'email'));

        $smsConfigured = Setting::get('sms_enabled', '0', 'sms') === '1'
            && filled(Setting::get('sms_api_key', '', 'sms'));

        $whatsappConfigured = Setting::get('whatsapp_enabled', '0', 'whatsapp') === '1'
            && filled(Setting::get('whatsapp_api_key', '', 'whatsapp'))
            && filled(Setting::get('whatsapp_phone_number_id', '', 'whatsapp'));

        $cards = [
            [
                'key' => 'payment',
                'label' => 'Payment / Razorpay',
                'description' => 'Collect course payments via Razorpay.',
                'configured' => $razorpay->isConfigured(),
                'enabled' => $razorpay->isConfigured(),
                'meta' => Setting::get('payment_mode', 'test', 'payment') === 'live' ? 'Live mode' : 'Test mode',
                'route' => route('platform.settings.index', ['tab' => 'payment']),
            ],
            [
                'key' => 'email',
                'label' => 'Email SMTP',
                'description' => 'Transactional email delivery.',
                'configured' => $emailConfigured,
                'enabled' => $emailConfigured,
                'meta' => Setting::get('smtp_host', '', 'email') ?: 'No host set',
                'route' => route('platform.settings.index', ['tab' => 'email']),
            ],
            [
                'key' => 'sms',
                'label' => 'SMS',
                'description' => 'SMS provider credentials (provider-ready).',
                'configured' => filled(Setting::get('sms_api_key', '', 'sms')),
                'enabled' => Setting::get('sms_enabled', '0', 'sms') === '1',
                'meta' => Setting::get('sms_provider', '', 'sms') ?: 'No provider',
                'route' => route('platform.settings.index', ['tab' => 'sms']),
            ],
            [
                'key' => 'whatsapp',
                'label' => 'WhatsApp',
                'description' => 'WhatsApp Business API credentials.',
                'configured' => filled(Setting::get('whatsapp_api_key', '', 'whatsapp')),
                'enabled' => Setting::get('whatsapp_enabled', '0', 'whatsapp') === '1',
                'meta' => Setting::get('whatsapp_provider', 'meta', 'whatsapp'),
                'route' => route('platform.settings.index', ['tab' => 'whatsapp']),
            ],
            [
                'key' => 'google',
                'label' => 'Google Login',
                'description' => 'OAuth signup & login for institutes.',
                'configured' => $google->isConfigured(),
                'enabled' => $google->isEnabled(),
                'meta' => $google->isEnabled() ? 'Enabled' : 'Disabled',
                'route' => route('platform.settings.index', ['tab' => 'google']),
            ],
        ];

        return view('platform.integrations.index', compact('cards'));
    }
}
