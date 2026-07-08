<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class PlatformSettingController extends Controller
{
    public function index()
    {
        $settings = [
            'site_name' => Setting::get('site_name', 'Learnyst'),
            'support_email' => Setting::get('support_email', 'support@learnyst.com'),
            'maintenance_mode' => Setting::get('maintenance_mode', '0'),
        ];

        return view('platform.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'support_email' => ['required', 'email'],
            'maintenance_mode' => ['nullable', 'boolean'],
        ]);

        Setting::set('site_name', $validated['site_name'], 'platform');
        Setting::set('support_email', $validated['support_email'], 'platform');
        Setting::set('maintenance_mode', $request->boolean('maintenance_mode') ? '1' : '0', 'platform');

        return back()->with('success', 'Platform settings updated.');
    }
}
