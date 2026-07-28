<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\ActivityLogger;
use App\Services\GoogleOAuthService;
use Illuminate\Http\Request;

class PlatformSettingController extends Controller
{
    public function index(GoogleOAuthService $google)
    {
        $settings = [
            'site_name' => Setting::get('site_name', 'StudyNest', 'platform'),
            'support_email' => Setting::get('support_email', 'support@studynest.com', 'platform'),
        ];

        $payment = [
            'razorpay_key' => Setting::get('razorpay_key', '', 'payment'),
            'razorpay_secret' => Setting::get('razorpay_secret', '', 'payment'),
            'currency' => Setting::get('currency', 'INR', 'payment'),
            'payment_mode' => Setting::get('payment_mode', 'test', 'payment'),
            'manual_payment_enabled' => Setting::get('manual_payment_enabled', '1', 'payment'),
            'gst_enabled' => Setting::get('gst_enabled', '0', 'payment'),
        ];

        $oauth = [
            'google_oauth_enabled' => Setting::get('google_oauth_enabled', '1', 'oauth'),
            'google_client_id' => Setting::get('google_client_id', '', 'oauth'),
            'google_client_secret' => Setting::get('google_client_secret', '', 'oauth'),
            'redirect_uri' => $google->redirectUri(),
            'is_configured' => $google->isConfigured(),
        ];

        $email = [
            'smtp_host' => Setting::get('smtp_host', '', 'email'),
            'smtp_port' => Setting::get('smtp_port', '587', 'email'),
            'smtp_username' => Setting::get('smtp_username', '', 'email'),
            'smtp_password' => Setting::get('smtp_password', '', 'email'),
            'smtp_encryption' => Setting::get('smtp_encryption', 'tls', 'email'),
            'mail_from_address' => Setting::get('mail_from_address', '', 'email'),
            'mail_from_name' => Setting::get('mail_from_name', 'StudyNest', 'email'),
        ];

        $sms = [
            'sms_provider' => Setting::get('sms_provider', '', 'sms'),
            'sms_api_key' => Setting::get('sms_api_key', '', 'sms'),
            'sms_api_secret' => Setting::get('sms_api_secret', '', 'sms'),
            'sms_sender_id' => Setting::get('sms_sender_id', '', 'sms'),
            'sms_enabled' => Setting::get('sms_enabled', '0', 'sms'),
        ];

        $whatsapp = [
            'whatsapp_provider' => Setting::get('whatsapp_provider', 'meta', 'whatsapp'),
            'whatsapp_api_key' => Setting::get('whatsapp_api_key', '', 'whatsapp'),
            'whatsapp_phone_number_id' => Setting::get('whatsapp_phone_number_id', '', 'whatsapp'),
            'whatsapp_business_account_id' => Setting::get('whatsapp_business_account_id', '', 'whatsapp'),
            'whatsapp_webhook_token' => Setting::get('whatsapp_webhook_token', '', 'whatsapp'),
            'whatsapp_enabled' => Setting::get('whatsapp_enabled', '0', 'whatsapp'),
        ];

        $security = $this->securitySettings();

        return view('platform.settings.index', compact(
            'settings',
            'payment',
            'oauth',
            'email',
            'sms',
            'whatsapp',
            'security'
        ));
    }

    public function update(Request $request)
    {
        $section = $request->input('section', 'general');

        return match ($section) {
            'payment' => $this->updatePayment($request),
            'google' => $this->updateGoogle($request),
            'email' => $this->updateEmail($request),
            'sms' => $this->updateSms($request),
            'whatsapp' => $this->updateWhatsapp($request),
            'security' => $this->updateSecurity($request),
            default => $this->updateGeneral($request),
        };
    }

    protected function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'support_email' => ['required', 'email'],
        ]);

        Setting::set('site_name', $validated['site_name'], 'platform');
        Setting::set('support_email', $validated['support_email'], 'platform');

        ActivityLogger::log('platform_settings_updated', 'General platform settings updated');

        return redirect()
            ->route('platform.settings.index', ['tab' => 'general'])
            ->with('success', 'General settings saved.');
    }

    protected function updatePayment(Request $request)
    {
        $validated = $request->validate([
            'razorpay_key' => ['nullable', 'string', 'max:255'],
            'razorpay_secret' => ['nullable', 'string', 'max:255'],
            'currency' => ['nullable', 'string', 'max:10'],
            'payment_mode' => ['nullable', 'in:test,live'],
            'manual_payment_enabled' => ['nullable', 'in:0,1'],
            'gst_enabled' => ['nullable', 'in:0,1'],
        ]);

        Setting::set('razorpay_key', $validated['razorpay_key'] ?? '', 'payment');
        if (filled($validated['razorpay_secret'] ?? null)) {
            Setting::set('razorpay_secret', $validated['razorpay_secret'], 'payment', 'password');
        }
        Setting::set('currency', $validated['currency'] ?? 'INR', 'payment');
        Setting::set('payment_mode', $validated['payment_mode'] ?? 'test', 'payment');
        Setting::set('manual_payment_enabled', $validated['manual_payment_enabled'] ?? '1', 'payment');
        Setting::set('gst_enabled', $validated['gst_enabled'] ?? '0', 'payment');

        ActivityLogger::log('payment_settings_updated', 'Razorpay payment settings updated');

        return redirect()
            ->route('platform.settings.index', ['tab' => 'payment'])
            ->with('success', 'Razorpay payment settings saved.');
    }

    protected function updateGoogle(Request $request)
    {
        $validated = $request->validate([
            'google_oauth_enabled' => ['nullable', 'in:0,1'],
            'google_client_id' => ['nullable', 'string', 'max:500'],
            'google_client_secret' => ['nullable', 'string', 'max:500'],
        ]);

        Setting::set('google_oauth_enabled', $validated['google_oauth_enabled'] ?? '0', 'oauth');
        Setting::set('google_client_id', trim((string) ($validated['google_client_id'] ?? '')), 'oauth');

        $secret = trim((string) ($validated['google_client_secret'] ?? ''));
        if ($secret !== '') {
            Setting::set('google_client_secret', $secret, 'oauth', 'password');
        }

        ActivityLogger::log('google_oauth_settings_updated', 'Google OAuth settings updated');

        return redirect()
            ->route('platform.settings.index', ['tab' => 'google'])
            ->with('success', 'Google signup/login settings saved.');
    }

    protected function updateEmail(Request $request)
    {
        $validated = $request->validate([
            'smtp_host' => ['nullable', 'string', 'max:255'],
            'smtp_port' => ['nullable', 'string', 'max:10'],
            'smtp_username' => ['nullable', 'string', 'max:255'],
            'smtp_password' => ['nullable', 'string', 'max:255'],
            'smtp_encryption' => ['nullable', 'in:tls,ssl,none'],
            'mail_from_address' => ['nullable', 'email', 'max:255'],
            'mail_from_name' => ['nullable', 'string', 'max:255'],
        ]);

        Setting::set('smtp_host', $validated['smtp_host'] ?? '', 'email');
        Setting::set('smtp_port', $validated['smtp_port'] ?? '587', 'email');
        Setting::set('smtp_username', $validated['smtp_username'] ?? '', 'email');
        if (filled($validated['smtp_password'] ?? null)) {
            Setting::set('smtp_password', $validated['smtp_password'], 'email', 'password');
        }
        Setting::set('smtp_encryption', $validated['smtp_encryption'] ?? 'tls', 'email');
        Setting::set('mail_from_address', $validated['mail_from_address'] ?? '', 'email');
        Setting::set('mail_from_name', $validated['mail_from_name'] ?? 'StudyNest', 'email');

        ActivityLogger::log('email_settings_updated', 'Platform SMTP email settings updated');

        return redirect()
            ->route('platform.settings.index', ['tab' => 'email'])
            ->with('success', 'Email SMTP settings saved.');
    }

    protected function updateSms(Request $request)
    {
        $validated = $request->validate([
            'sms_provider' => ['nullable', 'string', 'max:100'],
            'sms_api_key' => ['nullable', 'string', 'max:500'],
            'sms_api_secret' => ['nullable', 'string', 'max:500'],
            'sms_sender_id' => ['nullable', 'string', 'max:50'],
            'sms_enabled' => ['nullable', 'in:0,1'],
        ]);

        Setting::set('sms_provider', $validated['sms_provider'] ?? '', 'sms');
        Setting::set('sms_api_key', $validated['sms_api_key'] ?? '', 'sms', 'password');
        if (filled($validated['sms_api_secret'] ?? null)) {
            Setting::set('sms_api_secret', $validated['sms_api_secret'], 'sms', 'password');
        }
        Setting::set('sms_sender_id', $validated['sms_sender_id'] ?? '', 'sms');
        Setting::set('sms_enabled', $validated['sms_enabled'] ?? '0', 'sms');

        ActivityLogger::log('sms_settings_updated', 'Platform SMS provider settings updated');

        return redirect()
            ->route('platform.settings.index', ['tab' => 'sms'])
            ->with('success', 'SMS provider settings saved.');
    }

    protected function updateWhatsapp(Request $request)
    {
        $validated = $request->validate([
            'whatsapp_provider' => ['nullable', 'string', 'max:100'],
            'whatsapp_api_key' => ['nullable', 'string', 'max:500'],
            'whatsapp_phone_number_id' => ['nullable', 'string', 'max:255'],
            'whatsapp_business_account_id' => ['nullable', 'string', 'max:255'],
            'whatsapp_webhook_token' => ['nullable', 'string', 'max:255'],
            'whatsapp_enabled' => ['nullable', 'in:0,1'],
        ]);

        Setting::set('whatsapp_provider', $validated['whatsapp_provider'] ?? 'meta', 'whatsapp');
        if (filled($validated['whatsapp_api_key'] ?? null)) {
            Setting::set('whatsapp_api_key', $validated['whatsapp_api_key'], 'whatsapp', 'password');
        }
        Setting::set('whatsapp_phone_number_id', $validated['whatsapp_phone_number_id'] ?? '', 'whatsapp');
        Setting::set('whatsapp_business_account_id', $validated['whatsapp_business_account_id'] ?? '', 'whatsapp');
        Setting::set('whatsapp_webhook_token', $validated['whatsapp_webhook_token'] ?? '', 'whatsapp', 'password');
        Setting::set('whatsapp_enabled', $validated['whatsapp_enabled'] ?? '0', 'whatsapp');

        ActivityLogger::log('whatsapp_settings_updated', 'Platform WhatsApp provider settings updated');

        return redirect()
            ->route('platform.settings.index', ['tab' => 'whatsapp'])
            ->with('success', 'WhatsApp provider settings saved.');
    }

    protected function updateSecurity(Request $request)
    {
        $validated = $request->validate([
            'maintenance_mode' => ['nullable', 'boolean'],
            'maintenance_message' => ['nullable', 'string', 'max:1000'],
            'ip_allowlist_enabled' => ['nullable', 'boolean'],
            'ip_allowlist' => ['nullable', 'string', 'max:5000'],
        ]);

        Setting::set('maintenance_mode', $request->boolean('maintenance_mode') ? '1' : '0', 'platform');
        Setting::set('maintenance_message', trim((string) ($validated['maintenance_message'] ?? '')), 'platform');
        Setting::set('ip_allowlist_enabled', $request->boolean('ip_allowlist_enabled') ? '1' : '0', 'security');
        Setting::set('ip_allowlist', trim((string) ($validated['ip_allowlist'] ?? '')), 'security');

        ActivityLogger::log('security_settings_updated', 'Platform security settings updated', null, [
            'maintenance_mode' => $request->boolean('maintenance_mode'),
            'ip_allowlist_enabled' => $request->boolean('ip_allowlist_enabled'),
        ]);

        return redirect()
            ->route('platform.settings.index', ['tab' => 'security'])
            ->with('success', 'Security settings saved.');
    }

    public function securitySettings(): array
    {
        return [
            'maintenance_mode' => Setting::get('maintenance_mode', '0', 'platform'),
            'maintenance_message' => Setting::get('maintenance_message', 'We are undergoing scheduled maintenance. Please check back soon.', 'platform'),
            'ip_allowlist_enabled' => Setting::get('ip_allowlist_enabled', '0', 'security'),
            'ip_allowlist' => Setting::get('ip_allowlist', '', 'security'),
        ];
    }
}
