<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPackage;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlatformSubscriptionPackageController extends Controller
{
    public function index()
    {
        $packages = SubscriptionPackage::query()
            ->ordered()
            ->get();

        return view('platform.subscription-packages.index', compact('packages'));
    }

    public function create()
    {
        $package = new SubscriptionPackage([
            'currency' => 'INR',
            'cta_label' => 'Start Free Trial',
            'is_active' => true,
            'sort_order' => (int) SubscriptionPackage::max('sort_order') + 1,
            'features' => [],
        ]);

        return view('platform.subscription-packages.form', [
            'package' => $package,
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $package = SubscriptionPackage::create($validated);

        ActivityLogger::log('subscription_package_created', "Package created: {$package->name}", $package);

        return redirect()
            ->route('platform.subscription-packages.index')
            ->with('success', 'Subscription package created.');
    }

    public function edit(SubscriptionPackage $subscriptionPackage)
    {
        return view('platform.subscription-packages.form', [
            'package' => $subscriptionPackage,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, SubscriptionPackage $subscriptionPackage)
    {
        $validated = $this->validated($request, $subscriptionPackage);
        $subscriptionPackage->update($validated);

        ActivityLogger::log('subscription_package_updated', "Package updated: {$subscriptionPackage->name}", $subscriptionPackage);

        return redirect()
            ->route('platform.subscription-packages.index')
            ->with('success', 'Subscription package updated.');
    }

    public function destroy(SubscriptionPackage $subscriptionPackage)
    {
        $name = $subscriptionPackage->name;
        $subscriptionPackage->delete();

        ActivityLogger::log('subscription_package_deleted', "Package deleted: {$name}");

        return redirect()
            ->route('platform.subscription-packages.index')
            ->with('success', 'Subscription package deleted.');
    }

    public function toggle(SubscriptionPackage $subscriptionPackage)
    {
        $subscriptionPackage->update([
            'is_active' => ! $subscriptionPackage->is_active,
        ]);

        $state = $subscriptionPackage->is_active ? 'published' : 'hidden';
        ActivityLogger::log('subscription_package_toggled', "Package {$state}: {$subscriptionPackage->name}", $subscriptionPackage);

        return back()->with('success', "Package marked as {$state}.");
    }

    protected function validated(Request $request, ?SubscriptionPackage $package = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => [
                'nullable',
                'string',
                'max:140',
                Rule::unique('subscription_packages', 'slug')->ignore($package?->id),
            ],
            'tagline' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price_monthly' => ['nullable', 'numeric', 'min:0'],
            'price_yearly' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:10'],
            'is_free' => ['nullable'],
            'is_custom' => ['nullable'],
            'trial_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'features' => ['nullable', 'array'],
            'features.*' => ['nullable', 'string', 'max:255'],
            'cta_label' => ['required', 'string', 'max:80'],
            'cta_url' => ['nullable', 'string', 'max:500'],
            'badge' => ['nullable', 'string', 'max:60'],
            'is_featured' => ['nullable'],
            'is_active' => ['nullable'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        $features = collect($validated['features'] ?? [])
            ->map(fn ($f) => trim((string) $f))
            ->filter()
            ->values()
            ->all();

        $slug = filled($validated['slug'] ?? null)
            ? $validated['slug']
            : SubscriptionPackage::uniqueSlug($validated['name'], $package?->id);

        return [
            'name' => $validated['name'],
            'slug' => $slug,
            'tagline' => $validated['tagline'] ?? null,
            'description' => $validated['description'] ?? null,
            'price_monthly' => $validated['price_monthly'] ?? null,
            'price_yearly' => $validated['price_yearly'] ?? null,
            'currency' => strtoupper($validated['currency']),
            'is_free' => $request->boolean('is_free'),
            'is_custom' => $request->boolean('is_custom'),
            'trial_days' => (int) ($validated['trial_days'] ?? 0),
            'features' => $features,
            'cta_label' => $validated['cta_label'],
            'cta_url' => $validated['cta_url'] ?? null,
            'badge' => $validated['badge'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active'),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
        ];
    }
}
