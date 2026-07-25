<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;

class GoogleOAuthService
{
    public function clientId(): ?string
    {
        $value = Setting::get('google_client_id', '', 'oauth')
            ?: config('services.google.client_id');

        return filled($value) ? (string) $value : null;
    }

    public function clientSecret(): ?string
    {
        $value = Setting::get('google_client_secret', '', 'oauth')
            ?: config('services.google.client_secret');

        return filled($value) ? (string) $value : null;
    }

    public function isEnabled(): bool
    {
        return Setting::get('google_oauth_enabled', '1', 'oauth') !== '0'
            && $this->isConfigured();
    }

    public function isConfigured(): bool
    {
        return filled($this->clientId()) && filled($this->clientSecret());
    }

    public function redirectUri(): string
    {
        return route('auth.google.callback');
    }

    /**
     * Push DB/env credentials into Socialite's services.google config.
     */
    public function configure(): void
    {
        Config::set('services.google', [
            'client_id' => $this->clientId(),
            'client_secret' => $this->clientSecret(),
            'redirect' => $this->redirectUri(),
        ]);
    }
}
