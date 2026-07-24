<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Coupon;
use App\Models\Lead;
use Illuminate\Http\Request;

class MarketingController extends Controller
{
    use ScopesToCurrentUser;

    public function coupons(Request $request)
    {
        $coupons = Coupon::latest()->paginate(15);

        return view('admin.marketing.coupons', compact('coupons'));
    }

    public function storeCoupon(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'unique:coupons,code'],
            'title' => ['nullable', 'string'],
            'discount_type' => ['required', 'in:fixed,percentage'],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'max_uses' => ['nullable', 'integer'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        Coupon::create($validated);

        return back()->with('success', 'Coupon created.');
    }

    public function destroyCoupon(Coupon $coupon)
    {
        $coupon->delete();

        return back()->with('success', 'Coupon deleted.');
    }

    public function campaigns()
    {
        $campaigns = $this->owned(Campaign::query())->latest()->paginate(15);

        return view('admin.marketing.campaigns', compact('campaigns'));
    }

    public function storeCampaign(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string'],
            'content' => ['nullable', 'string'],
            'channel' => ['required', 'in:email,whatsapp,both'],
            'scheduled_at' => ['nullable', 'date'],
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'draft';

        Campaign::create($validated);

        return back()->with('success', 'Campaign created.');
    }

    public function leads()
    {
        $leads = Lead::with('course')->latest()->paginate(20);

        return view('admin.marketing.leads', compact('leads'));
    }

    public function storeLead(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string'],
            'source' => ['nullable', 'string'],
            'course_id' => ['nullable', 'exists:courses,id'],
            'notes' => ['nullable', 'string'],
        ]);

        Lead::create($validated);

        return back()->with('success', 'Lead captured.');
    }
}
