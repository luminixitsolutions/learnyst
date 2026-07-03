<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function socialLinks()
    {
        $links = Setting::where('group', 'social')->get()->pluck('value', 'key');

        return view('admin.settings.social', compact('links'));
    }

    public function updateSocialLinks(Request $request)
    {
        $keys = ['facebook', 'youtube', 'linkedin', 'telegram', 'whatsapp', 'instagram', 'website'];

        foreach ($keys as $key) {
            Setting::set($key, $request->input($key, ''), 'social', 'url');
        }

        ActivityLogger::log('social_links_updated', 'Social links updated');

        return back()->with('success', 'Social links saved.');
    }

    public function index()
    {
        $groups = [
            'general' => Setting::where('group', 'general')->get()->pluck('value', 'key'),
            'payment' => Setting::where('group', 'payment')->get()->pluck('value', 'key'),
            'tax' => Setting::where('group', 'tax')->get()->pluck('value', 'key'),
            'email' => Setting::where('group', 'email')->get()->pluck('value', 'key'),
            'whatsapp' => Setting::where('group', 'whatsapp')->get()->pluck('value', 'key'),
            'invoice' => Setting::where('group', 'invoice')->get()->pluck('value', 'key'),
        ];

        return view('admin.settings.index', compact('groups'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => ['required', 'array'],
            'settings.*.group' => ['required', 'string'],
            'settings.*.key' => ['required', 'string'],
            'settings.*.value' => ['nullable', 'string'],
        ]);

        foreach ($validated['settings'] as $item) {
            Setting::set($item['key'], $item['value'] ?? '', $item['group']);
        }

        ActivityLogger::log('settings_updated', 'Platform settings updated');

        return back()->with('success', 'Settings saved successfully.');
    }

    public function uploadLogo(Request $request)
    {
        $request->validate(['logo' => ['required', 'image', 'max:2048']]);
        $path = $request->file('logo')->store('settings', 'public');
        Setting::set('logo', $path, 'general', 'file');

        return back()->with('success', 'Logo uploaded.');
    }
}
