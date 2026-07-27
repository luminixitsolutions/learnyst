<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use App\Services\CompanyService;
use App\Services\SidebarSettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SidebarSettingsController extends Controller
{
    public function edit()
    {
        $company = CompanyService::resolveForUser(Auth::user());
        $settings = SidebarSettingsService::forCompany($company);
        $menuSections = collect(config('admin-menu.admin', []));
        $menuItems = SidebarSettingsService::menuOrderLabels(
            SidebarSettingsService::reorderSections($menuSections->all(), $settings['menu_order'])
        );
        $themes = SidebarSettingsService::THEMES;

        return view('admin.settings.sidebar', compact('company', 'settings', 'menuItems', 'themes'));
    }

    public function update(Request $request)
    {
        $company = CompanyService::resolveForUser(Auth::user());

        $validKeys = collect(config('admin-menu.admin', []))
            ->map(fn ($section) => SidebarSettingsService::sectionKey($section))
            ->all();

        $validated = $request->validate([
            'layout' => ['required', 'in:vertical,horizontal'],
            'theme' => ['required', 'in:'.implode(',', SidebarSettingsService::allowedThemeKeys())],
            'custom_primary' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'custom_secondary' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'custom_bg_start' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'custom_bg_end' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'menu_order' => ['nullable', 'array'],
            'menu_order.*' => ['string', 'in:'.implode(',', $validKeys)],
        ]);

        if ($validated['theme'] === 'custom') {
            $request->validate([
                'custom_primary' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
                'custom_secondary' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
                'custom_bg_start' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
                'custom_bg_end' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            ]);
        }

        SidebarSettingsService::save($company, $request->only([
            'layout',
            'theme',
            'custom_primary',
            'custom_secondary',
            'custom_bg_start',
            'custom_bg_end',
            'menu_order',
        ]));

        ActivityLogger::log('sidebar_settings_updated', 'Sidebar settings updated', $company);

        return redirect()
            ->route('admin.settings.sidebar')
            ->with('success', 'Sidebar settings saved successfully.');
    }

    public function reset()
    {
        $company = CompanyService::resolveForUser(Auth::user());
        $profile = $company->profile ?? [];
        unset($profile['sidebar']);
        $company->update(['profile' => $profile]);

        ActivityLogger::log('sidebar_settings_reset', 'Sidebar settings reset to defaults', $company);

        return redirect()
            ->route('admin.settings.sidebar')
            ->with('success', 'Sidebar settings reset to defaults.');
    }
}
