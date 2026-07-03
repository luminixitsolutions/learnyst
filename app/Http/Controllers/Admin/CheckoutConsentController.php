<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CheckoutConsent;
use App\Models\OrderConsent;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class CheckoutConsentController extends Controller
{
    public function index(Request $request)
    {
        $consents = CheckoutConsent::withCount('orderConsents')->orderBy('sort_order')->paginate(15);
        $totalAcceptances = OrderConsent::where('accepted', true)->count();

        return view('admin.checkout-consents.index', compact('consents', 'totalAcceptances'));
    }

    public function create()
    {
        return view('admin.checkout-consents.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'body' => ['required', 'string'],
            'is_required' => ['boolean'],
            'is_active' => ['boolean'],
            'show_on_checkout' => ['boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $validated['is_required'] = $request->boolean('is_required', true);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['show_on_checkout'] = $request->boolean('show_on_checkout', true);

        $consent = CheckoutConsent::create($validated);
        ActivityLogger::log('consent_created', "Consent {$consent->title} created", $consent);

        return redirect()->route('admin.checkout-consents.index')->with('success', 'Consent created.');
    }

    public function edit(CheckoutConsent $checkoutConsent)
    {
        return view('admin.checkout-consents.edit', compact('checkoutConsent'));
    }

    public function update(Request $request, CheckoutConsent $checkoutConsent)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'body' => ['required', 'string'],
            'is_required' => ['boolean'],
            'is_active' => ['boolean'],
            'show_on_checkout' => ['boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $validated['is_required'] = $request->boolean('is_required', true);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['show_on_checkout'] = $request->boolean('show_on_checkout', true);

        $checkoutConsent->update($validated);

        return redirect()->route('admin.checkout-consents.index')->with('success', 'Consent updated.');
    }

    public function destroy(CheckoutConsent $checkoutConsent)
    {
        $checkoutConsent->delete();

        return redirect()->route('admin.checkout-consents.index')->with('success', 'Consent deleted.');
    }

    public function report()
    {
        $records = OrderConsent::with(['order', 'consent', 'user'])
            ->where('accepted', true)
            ->latest('accepted_at')
            ->paginate(30);

        return view('admin.checkout-consents.report', compact('records'));
    }
}
