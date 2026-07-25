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
            'site_name' => Setting::get('site_name', 'Learnyst', 'platform'),
            'support_email' => Setting::get('support_email', 'support@learnyst.com', 'platform'),
            'maintenance_mode' => Setting::get('maintenance_mode', '0', 'platform'),
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

        return view('platform.settings.index', compact('settings', 'payment', 'oauth'));
    }

    public function update(Request $request)
    {
        $section = $request->input('section', 'general');

        if ($section === 'payment') {
            $validated = $request->validate([
                'razorpay_key' => ['nullable', 'string', 'max:255'],
                'razorpay_secret' => ['nullable', 'string', 'max:255'],
                'currency' => ['nullable', 'string', 'max:10'],
                'payment_mode' => ['nullable', 'in:test,live'],
                'manual_payment_enabled' => ['nullable', 'in:0,1'],
                'gst_enabled' => ['nullable', 'in:0,1'],
            ]);

            Setting::set('razorpay_key', $validated['razorpay_key'] ?? '', 'payment');
            Setting::set('razorpay_secret', $validated['razorpay_secret'] ?? '', 'payment', 'password');
            Setting::set('currency', $validated['currency'] ?? 'INR', 'payment');
            Setting::set('payment_mode', $validated['payment_mode'] ?? 'test', 'payment');
            Setting::set('manual_payment_enabled', $validated['manual_payment_enabled'] ?? '1', 'payment');
            Setting::set('gst_enabled', $validated['gst_enabled'] ?? '0', 'payment');

            ActivityLogger::log('payment_settings_updated', 'Razorpay payment settings updated');

            return redirect()
                ->route('platform.settings.index', ['tab' => 'payment'])
                ->with('success', 'Razorpay payment settings saved.');
        }

        if ($section === 'google') {
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

        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'support_email' => ['required', 'email'],
            'maintenance_mode' => ['nullable', 'boolean'],
        ]);

        Setting::set('site_name', $validated['site_name'], 'platform');
        Setting::set('support_email', $validated['support_email'], 'platform');
        Setting::set('maintenance_mode', $request->boolean('maintenance_mode') ? '1' : '0', 'platform');

        return redirect()
            ->route('platform.settings.index')
            ->with('success', 'Platform settings updated.');
    }
}
