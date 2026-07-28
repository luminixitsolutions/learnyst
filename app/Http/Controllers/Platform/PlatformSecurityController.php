<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class PlatformSecurityController extends Controller
{
    public function index()
    {
        $security = [
            'maintenance_mode' => Setting::get('maintenance_mode', '0', 'platform'),
            'maintenance_message' => Setting::get('maintenance_message', 'We are undergoing scheduled maintenance. Please check back soon.', 'platform'),
            'ip_allowlist_enabled' => Setting::get('ip_allowlist_enabled', '0', 'security'),
            'ip_allowlist' => Setting::get('ip_allowlist', '', 'security'),
        ];

        return view('platform.security.index', compact('security'));
    }

    public function update(Request $request)
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
            ->route('platform.security.index')
            ->with('success', 'Security settings saved.');
    }
}
