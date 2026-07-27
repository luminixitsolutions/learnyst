<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class ProctoringController extends Controller
{
    public function index()
    {
        $settings = [
            'webcam_required' => Setting::get('proctoring_webcam_required', '0', 'proctoring') === '1',
            'tab_switch_detection' => Setting::get('proctoring_tab_switch', '1', 'proctoring') === '1',
            'lockdown_mode' => Setting::get('proctoring_lockdown', '0', 'proctoring') === '1',
            'incident_retention_days' => (int) Setting::get('proctoring_retention_days', '90', 'proctoring'),
        ];

        return view('admin.proctoring.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'webcam_required' => ['boolean'],
            'tab_switch_detection' => ['boolean'],
            'lockdown_mode' => ['boolean'],
            'incident_retention_days' => ['required', 'integer', 'min:7', 'max:365'],
        ]);

        Setting::updateOrCreate(['group' => 'proctoring', 'key' => 'proctoring_webcam_required'], [
            'value' => $request->boolean('webcam_required') ? '1' : '0',
            'type' => 'boolean',
        ]);
        Setting::updateOrCreate(['group' => 'proctoring', 'key' => 'proctoring_tab_switch'], [
            'value' => $request->boolean('tab_switch_detection') ? '1' : '0',
            'type' => 'boolean',
        ]);
        Setting::updateOrCreate(['group' => 'proctoring', 'key' => 'proctoring_lockdown'], [
            'value' => $request->boolean('lockdown_mode') ? '1' : '0',
            'type' => 'boolean',
        ]);
        Setting::updateOrCreate(['group' => 'proctoring', 'key' => 'proctoring_retention_days'], [
            'value' => (string) $validated['incident_retention_days'],
            'type' => 'number',
        ]);

        ActivityLogger::log('proctoring_settings_updated', 'Proctoring defaults updated');

        return back()->with('success', 'Proctoring settings saved.');
    }
}
