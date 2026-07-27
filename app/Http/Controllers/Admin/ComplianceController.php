<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CheckoutConsent;
use App\Models\Setting;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class ComplianceController extends Controller
{
    public function index()
    {
        $settings = [
            'dpdp_enabled' => Setting::get('compliance_dpdp_enabled', '1', 'compliance') === '1',
            'gdpr_enabled' => Setting::get('compliance_gdpr_enabled', '0', 'compliance') === '1',
            'data_retention_days' => (int) Setting::get('compliance_retention_days', '365', 'compliance'),
            'consent_version' => Setting::get('compliance_consent_version', '1.0', 'compliance'),
        ];

        $consentTemplates = CheckoutConsent::orderBy('sort_order')->get();

        return view('admin.compliance.index', compact('settings', 'consentTemplates'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'dpdp_enabled' => ['boolean'],
            'gdpr_enabled' => ['boolean'],
            'data_retention_days' => ['required', 'integer', 'min:30', 'max:3650'],
            'consent_version' => ['required', 'string', 'max:20'],
        ]);

        Setting::updateOrCreate(['group' => 'compliance', 'key' => 'compliance_dpdp_enabled'], [
            'value' => $request->boolean('dpdp_enabled') ? '1' : '0',
            'type' => 'boolean',
        ]);
        Setting::updateOrCreate(['group' => 'compliance', 'key' => 'compliance_gdpr_enabled'], [
            'value' => $request->boolean('gdpr_enabled') ? '1' : '0',
            'type' => 'boolean',
        ]);
        Setting::updateOrCreate(['group' => 'compliance', 'key' => 'compliance_retention_days'], [
            'value' => (string) $validated['data_retention_days'],
            'type' => 'number',
        ]);
        Setting::updateOrCreate(['group' => 'compliance', 'key' => 'compliance_consent_version'], [
            'value' => $validated['consent_version'],
            'type' => 'text',
        ]);

        ActivityLogger::log('compliance_settings_updated', 'Compliance center settings updated');

        return back()->with('success', 'Compliance settings saved.');
    }
}
