<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class CompanyBrandingService
{
    public function ensureVerificationToken(Company $company): Company
    {
        if (! $company->domain_verification_token) {
            $company->update([
                'domain_verification_token' => 'sn-verify-'.Str::lower(Str::random(24)),
            ]);
        }

        return $company->fresh();
    }

    public function applyMailFrom(Company $company): void
    {
        if ($company->email_from_address) {
            Config::set('mail.from.address', $company->email_from_address);
            Config::set('mail.from.name', $company->email_from_name ?: $company->name);
        }
    }

    public function cssVariables(Company $company): string
    {
        $primary = $company->primary_color ?: '#059669';
        $secondary = $company->secondary_color ?: '#0f172a';
        $tokens = $company->theme_tokens ?? [];

        $vars = [
            '--brand-primary' => $primary,
            '--brand-secondary' => $secondary,
            '--brand-accent' => $tokens['accent'] ?? $primary,
        ];

        $css = ':root{';
        foreach ($vars as $k => $v) {
            $css .= "{$k}:{$v};";
        }

        return $css.'}';
    }

    public function dnsInstructions(Company $company): array
    {
        $company = $this->ensureVerificationToken($company);
        $domain = $company->custom_domain ?: 'your.domain.com';

        return [
            [
                'type' => 'CNAME',
                'host' => $domain,
                'value' => parse_url(config('app.url'), PHP_URL_HOST) ?: 'app.studynest.com',
                'note' => 'Point your custom domain to the StudyNest app host.',
            ],
            [
                'type' => 'TXT',
                'host' => '_studynest-verify.'.$domain,
                'value' => $company->domain_verification_token,
                'note' => 'Used to verify domain ownership (web white-label only — no mobile apps).',
            ],
        ];
    }

    public function markVerified(Company $company): void
    {
        $company->update(['domain_verified_at' => now()]);
    }
}
