<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class IntegrationService
{
    public const PROVIDERS = [
        'payment' => ['label' => 'Payment (Razorpay)', 'keys' => ['key_id', 'key_secret', 'webhook_secret', 'enabled']],
        'sms' => ['label' => 'SMS Provider', 'keys' => ['provider', 'api_key', 'sender_id', 'enabled']],
        'whatsapp' => ['label' => 'WhatsApp API', 'keys' => ['api_url', 'api_token', 'phone_number_id', 'enabled']],
        'zoom' => ['label' => 'Zoom', 'keys' => ['account_id', 'client_id', 'client_secret', 'enabled']],
        'google_meet' => ['label' => 'Google Meet', 'keys' => ['client_id', 'client_secret', 'enabled']],
        'microsoft_teams' => ['label' => 'Microsoft Teams', 'keys' => ['tenant_id', 'client_id', 'client_secret', 'enabled']],
        'smtp' => ['label' => 'Email SMTP', 'keys' => ['host', 'port', 'username', 'password', 'encryption', 'from_address', 'from_name', 'enabled']],
        'analytics' => ['label' => 'Analytics', 'keys' => ['provider', 'tracking_id', 'enabled']],
        'ai' => ['label' => 'AI APIs', 'keys' => ['base_url', 'model', 'api_key', 'enabled']],
        'telegram' => ['label' => 'Telegram Bot', 'keys' => ['bot_token', 'default_chat_id', 'enabled']],
        'oauth_facebook' => ['label' => 'Facebook Login', 'keys' => ['client_id', 'client_secret', 'enabled']],
        'oauth_apple' => ['label' => 'Apple Login', 'keys' => ['client_id', 'client_secret', 'team_id', 'key_id', 'enabled']],
        'oauth_linkedin' => ['label' => 'LinkedIn Login', 'keys' => ['client_id', 'client_secret', 'enabled']],
    ];

    protected array $secretKeys = [
        'key_secret', 'webhook_secret', 'api_key', 'api_token', 'client_secret',
        'password', 'bot_token',
    ];

    public function groupFor(?int $userId, string $provider): string
    {
        return 'integration_'.($userId ?: 'platform').'_'.$provider;
    }

    public function get(string $provider, ?int $userId = null): array
    {
        $keys = self::PROVIDERS[$provider]['keys'] ?? [];
        $group = $this->groupFor($userId, $provider);
        $out = [];

        foreach ($keys as $key) {
            $raw = Setting::get($key, null, $group);
            if ($raw && in_array($key, $this->secretKeys, true)) {
                try {
                    $out[$key] = Crypt::decryptString($raw);
                    $out[$key.'_set'] = true;
                } catch (\Throwable) {
                    $out[$key] = $raw;
                    $out[$key.'_set'] = filled($raw);
                }
            } else {
                $out[$key] = $raw;
            }
        }

        return $out;
    }

    public function save(string $provider, array $data, ?int $userId = null): void
    {
        $keys = self::PROVIDERS[$provider]['keys'] ?? [];
        $group = $this->groupFor($userId, $provider);

        foreach ($keys as $key) {
            if (! array_key_exists($key, $data)) {
                continue;
            }
            $value = $data[$key];
            if ($value === null || $value === '') {
                continue;
            }
            if (in_array($key, $this->secretKeys, true)) {
                Setting::set($key, Crypt::encryptString((string) $value), $group, 'encrypted');
            } elseif ($key === 'enabled') {
                Setting::set($key, $value ? '1' : '0', $group);
            } else {
                Setting::set($key, (string) $value, $group);
            }
        }
    }

    public function status(string $provider, ?int $userId = null): string
    {
        $cfg = $this->get($provider, $userId);
        if (($cfg['enabled'] ?? '0') === '0' || ($cfg['enabled'] ?? false) === false) {
            return 'disabled';
        }

        return match ($provider) {
            'payment' => filled($cfg['key_id'] ?? null) && filled($cfg['key_secret'] ?? null) ? 'connected' : 'incomplete',
            'sms' => filled($cfg['api_key'] ?? null) ? 'connected' : 'incomplete',
            'whatsapp' => filled($cfg['api_token'] ?? null) ? 'connected' : 'incomplete',
            'zoom' => filled($cfg['client_id'] ?? null) && filled($cfg['client_secret'] ?? null) ? 'connected' : 'incomplete',
            'google_meet', 'microsoft_teams', 'oauth_facebook', 'oauth_linkedin' => filled($cfg['client_id'] ?? null) && filled($cfg['client_secret'] ?? null) ? 'connected' : 'incomplete',
            'oauth_apple' => filled($cfg['client_id'] ?? null) ? 'connected' : 'incomplete',
            'smtp' => filled($cfg['host'] ?? null) && filled($cfg['from_address'] ?? null) ? 'connected' : 'incomplete',
            'analytics' => filled($cfg['tracking_id'] ?? null) ? 'connected' : 'incomplete',
            'ai' => filled($cfg['api_key'] ?? null) ? 'connected' : 'incomplete',
            'telegram' => filled($cfg['bot_token'] ?? null) ? 'connected' : 'incomplete',
            default => 'unknown',
        };
    }

    public function test(string $provider, ?int $userId = null): array
    {
        $cfg = $this->get($provider, $userId);

        return match ($provider) {
            'smtp' => $this->testSmtp($cfg),
            'telegram' => $this->testTelegram($cfg),
            'ai' => $this->testAi($cfg),
            'payment' => [
                'ok' => filled($cfg['key_id'] ?? null) && filled($cfg['key_secret'] ?? null),
                'message' => filled($cfg['key_id'] ?? null) ? 'Razorpay keys present (live charge not attempted).' : 'Missing Razorpay keys.',
            ],
            'sms', 'whatsapp', 'zoom', 'google_meet', 'microsoft_teams', 'analytics',
            'oauth_facebook', 'oauth_apple', 'oauth_linkedin' => [
                'ok' => $this->status($provider, $userId) === 'connected',
                'message' => $this->status($provider, $userId) === 'connected'
                    ? 'Credentials look complete. Provider API call skipped in test mode.'
                    : 'Incomplete credentials.',
            ],
            default => ['ok' => false, 'message' => 'Unknown provider'],
        };
    }

    protected function testSmtp(array $cfg): array
    {
        if (! filled($cfg['host'] ?? null) || ! filled($cfg['from_address'] ?? null)) {
            return ['ok' => false, 'message' => 'SMTP host/from address required.'];
        }

        try {
            Config::set('mail.mailers.smtp', [
                'transport' => 'smtp',
                'host' => $cfg['host'],
                'port' => (int) ($cfg['port'] ?? 587),
                'encryption' => $cfg['encryption'] ?? 'tls',
                'username' => $cfg['username'] ?? null,
                'password' => $cfg['password'] ?? null,
            ]);
            Config::set('mail.default', 'smtp');
            Config::set('mail.from.address', $cfg['from_address']);
            Config::set('mail.from.name', $cfg['from_name'] ?? 'StudyNest');

            return ['ok' => true, 'message' => 'SMTP settings applied for this request. Send a real email to fully verify.'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }

    protected function testTelegram(array $cfg): array
    {
        $token = $cfg['bot_token'] ?? null;
        if (! $token) {
            return ['ok' => false, 'message' => 'Bot token missing.'];
        }

        try {
            $res = Http::timeout(10)->get("https://api.telegram.org/bot{$token}/getMe");
            if ($res->successful() && ($res->json('ok') === true)) {
                return ['ok' => true, 'message' => 'Bot OK: '.($res->json('result.username') ?? 'connected')];
            }

            return ['ok' => false, 'message' => $res->json('description') ?? 'Telegram API error'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }

    protected function testAi(array $cfg): array
    {
        if (! filled($cfg['api_key'] ?? null)) {
            return ['ok' => false, 'message' => 'API key missing.'];
        }

        return ['ok' => true, 'message' => 'AI key stored. Use AI Center to run a generation test.'];
    }

    public function applySocialiteConfig(?int $userId = null): void
    {
        $fb = $this->get('oauth_facebook', $userId);
        if (($fb['enabled'] ?? '0') === '1' && filled($fb['client_id'] ?? null)) {
            Config::set('services.facebook', [
                'client_id' => $fb['client_id'],
                'client_secret' => $fb['client_secret'] ?? '',
                'redirect' => route('auth.social.callback', 'facebook'),
            ]);
        }

        $li = $this->get('oauth_linkedin', $userId);
        if (($li['enabled'] ?? '0') === '1' && filled($li['client_id'] ?? null)) {
            Config::set('services.linkedin-openid', [
                'client_id' => $li['client_id'],
                'client_secret' => $li['client_secret'] ?? '',
                'redirect' => route('auth.social.callback', 'linkedin'),
            ]);
            Config::set('services.linkedin', [
                'client_id' => $li['client_id'],
                'client_secret' => $li['client_secret'] ?? '',
                'redirect' => route('auth.social.callback', 'linkedin'),
            ]);
        }

        $apple = $this->get('oauth_apple', $userId);
        if (($apple['enabled'] ?? '0') === '1' && filled($apple['client_id'] ?? null)) {
            Config::set('services.apple', [
                'client_id' => $apple['client_id'],
                'client_secret' => $apple['client_secret'] ?? '',
                'redirect' => route('auth.social.callback', 'apple'),
            ]);
        }
    }
}