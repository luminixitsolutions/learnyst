<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\ActivityLogger;
use App\Services\CompanyBrandingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WhiteLabelController extends Controller
{
    public function __construct(protected CompanyBrandingService $branding) {}

    public function edit()
    {
        $company = Company::firstOrCreateForOwner(Auth::user());
        $company = $this->branding->ensureVerificationToken($company);
        $dns = $this->branding->dnsInstructions($company);

        return view('admin.whitelabel.edit', compact('company', 'dns'));
    }

    public function update(Request $request)
    {
        $company = Company::firstOrCreateForOwner(Auth::user());

        $validated = $request->validate([
            'custom_domain' => ['nullable', 'string', 'max:180'],
            'primary_color' => ['nullable', 'string', 'max:20'],
            'secondary_color' => ['nullable', 'string', 'max:20'],
            'email_from_name' => ['nullable', 'string', 'max:120'],
            'email_from_address' => ['nullable', 'email', 'max:180'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'favicon' => ['nullable', 'image', 'max:512'],
            'accent' => ['nullable', 'string', 'max:20'],
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('companies/logos', 'public');
        } else {
            unset($validated['logo']);
        }

        if ($request->hasFile('favicon')) {
            $validated['favicon'] = $request->file('favicon')->store('companies/favicons', 'public');
        } else {
            unset($validated['favicon']);
        }

        $tokens = $company->theme_tokens ?? [];
        if (! empty($validated['accent'])) {
            $tokens['accent'] = $validated['accent'];
        }
        unset($validated['accent']);
        $validated['theme_tokens'] = $tokens;

        if (! empty($validated['custom_domain']) && $validated['custom_domain'] !== $company->custom_domain) {
            $validated['domain_verified_at'] = null;
        }

        $company->update($validated);
        $this->branding->applyMailFrom($company->fresh());

        ActivityLogger::log('whitelabel_updated', "White-label branding updated for {$company->name}", $company);

        return back()->with('success', 'Branding saved (web white-label only — no mobile apps).');
    }

    public function markVerified()
    {
        $company = Company::firstOrCreateForOwner(Auth::user());
        $this->branding->markVerified($company);

        return back()->with('success', 'Domain marked as verified. Ensure DNS records are live in production.');
    }
}
