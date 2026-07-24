<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\ActivityLogger;
use App\Services\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompanyProfileController extends Controller
{
    public function edit()
    {
        $company = CompanyService::resolveForUser(Auth::user());
        $publicUrl = route('website.companies.show', $company->slug);

        return view('admin.company-profile.edit', compact('company', 'publicUrl'));
    }

    public function update(Request $request)
    {
        $company = CompanyService::resolveForUser(Auth::user());

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:120', 'alpha_dash'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'about' => ['nullable', 'string', 'max:8000'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:120'],
            'highlights' => ['nullable', 'string', 'max:2000'],
            'is_public' => ['nullable'],
            'logo' => ['nullable', 'image', 'max:4096'],
            'cover_image' => ['nullable', 'image', 'max:6144'],
            'social_links' => ['nullable', 'array'],
            'social_links.*' => ['nullable', 'string', 'max:255'],
            'remove_logo' => ['nullable'],
            'remove_cover' => ['nullable'],
            'profile' => ['nullable', 'array'],
            'profile.mission' => ['nullable', 'string', 'max:2000'],
            'profile.vision' => ['nullable', 'string', 'max:2000'],
            'profile.founded_year' => ['nullable', 'string', 'max:10'],
            'profile.state' => ['nullable', 'string', 'max:120'],
            'profile.country' => ['nullable', 'string', 'max:120'],
            'profile.working_hours' => ['nullable', 'string', 'max:120'],
            'profile.specialties' => ['nullable', 'string', 'max:2000'],
            'profile.stats' => ['nullable', 'array'],
            'profile.why_us' => ['nullable', 'array'],
            'profile.faqs' => ['nullable', 'array'],
        ]);

        $slugInput = trim((string) ($validated['slug'] ?? ''));
        $slug = $slugInput !== ''
            ? Str::slug($slugInput)
            : Company::uniqueSlug($validated['name'], $company->id);

        if (Company::query()->where('slug', $slug)->where('id', '!=', $company->id)->exists()) {
            $slug = Company::uniqueSlug($validated['name'], $company->id);
        }

        $logo = $company->logo;
        if ($request->boolean('remove_logo') && $logo) {
            Storage::disk('public')->delete($logo);
            $logo = null;
        }
        if ($request->hasFile('logo')) {
            if ($logo) {
                Storage::disk('public')->delete($logo);
            }
            $logo = $request->file('logo')->store('companies/logos', 'public');
        }

        $cover = $company->cover_image;
        if ($request->boolean('remove_cover') && $cover) {
            Storage::disk('public')->delete($cover);
            $cover = null;
        }
        if ($request->hasFile('cover_image')) {
            if ($cover) {
                Storage::disk('public')->delete($cover);
            }
            $cover = $request->file('cover_image')->store('companies/covers', 'public');
        }

        $existing = $company->profile ?? [];
        $profile = CompanyService::normalizeProfile($validated['profile'] ?? [], $existing);
        // Keep any legacy gallery/team JSON untouched while dedicated modules manage content.
        $profile['gallery'] = $existing['gallery'] ?? [];
        $profile['team'] = $existing['team'] ?? [];

        $company->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'tagline' => $validated['tagline'] ?? null,
            'about' => $validated['about'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'website_url' => $validated['website_url'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'highlights' => CompanyService::normalizeHighlights($validated['highlights'] ?? ''),
            'social_links' => CompanyService::normalizeSocialLinks($validated['social_links'] ?? []),
            'profile' => $profile,
            'is_public' => $request->boolean('is_public'),
            'logo' => $logo,
            'cover_image' => $cover,
        ]);

        if ($company->owner && $company->owner->isAdmin()) {
            $company->owner->forceFill([
                'name' => $company->name,
                'phone' => $company->phone ?: $company->owner->phone,
                'address' => $company->address ?: $company->owner->address,
                'bio' => $company->about ?: $company->owner->bio,
            ])->save();
        }

        ActivityLogger::log('company_profile_updated', "Institute profile updated: {$company->name}");

        return redirect()
            ->route('admin.company-profile.edit')
            ->with('success', 'Institute profile saved. Your public page is up to date.');
    }
}
