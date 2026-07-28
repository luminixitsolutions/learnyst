<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignSend;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\Lead;
use App\Models\Segment;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\CampaignDispatchService;
use App\Services\LeadConversionService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MarketingController extends Controller
{
    use ScopesToCurrentUser;

    public function __construct(
        protected LeadConversionService $conversions,
        protected CampaignDispatchService $dispatcher
    ) {}

    public function coupons(Request $request)
    {
        $coupons = $this->owned(Coupon::query())
            ->with('courses')
            ->withCount('orders')
            ->latest()
            ->paginate(15);

        $courses = $this->owned(Course::query())->orderBy('title')->get(['id', 'title']);

        return view('admin.marketing.coupons', compact('coupons', 'courses'));
    }

    public function storeCoupon(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:60', 'unique:coupons,code'],
            'title' => ['nullable', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:1000'],
            'discount_type' => ['required', 'in:fixed,percentage'],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'per_user_limit' => ['nullable', 'integer', 'min:1'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['boolean'],
            'course_ids' => ['nullable', 'array'],
            'course_ids.*' => [Rule::in($this->ownedCourseIds())],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['created_by'] = auth()->id();
        $validated['code'] = strtoupper(trim($validated['code']));
        $courseIds = $validated['course_ids'] ?? [];
        unset($validated['course_ids']);

        $coupon = Coupon::create($validated);
        if ($courseIds) {
            $coupon->courses()->sync($courseIds);
        }

        ActivityLogger::log('coupon_created', "Coupon {$coupon->code} created", $coupon);

        return back()->with('success', 'Coupon created.');
    }

    public function destroyCoupon(Coupon $coupon)
    {
        $this->authorizeOwner($coupon);
        $coupon->delete();

        return back()->with('success', 'Coupon deleted.');
    }

    public function campaigns()
    {
        $campaigns = $this->owned(Campaign::query())
            ->with('segment')
            ->withCount('sends')
            ->latest()
            ->paginate(15);

        $segments = Segment::where('is_active', true)->orderBy('title')->get();

        return view('admin.marketing.campaigns', compact('campaigns', 'segments'));
    }

    public function storeCampaign(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'subject' => ['nullable', 'string', 'max:180'],
            'content' => ['nullable', 'string'],
            'channel' => ['required', 'in:email,sms,whatsapp,both,email_sms,all'],
            'segment_id' => ['nullable', 'exists:segments,id'],
            'scheduled_at' => ['nullable', 'date'],
            'status' => ['nullable', 'in:draft,scheduled'],
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = filled($validated['scheduled_at'] ?? null) ? 'scheduled' : ($validated['status'] ?? 'draft');

        $campaign = Campaign::create($validated);

        ActivityLogger::log('campaign_created', "Campaign {$campaign->title} created", $campaign);

        return back()->with('success', 'Campaign created.');
    }

    public function sendCampaign(Campaign $campaign)
    {
        $this->authorizeOwner($campaign);
        $this->dispatcher->dispatch($campaign);

        return back()->with('success', 'Campaign dispatched. Check send logs for details.');
    }

    public function campaignSends(Campaign $campaign)
    {
        $this->authorizeOwner($campaign);
        $sends = CampaignSend::where('campaign_id', $campaign->id)
            ->with(['user', 'lead'])
            ->latest()
            ->paginate(30);

        return view('admin.marketing.campaign-sends', compact('campaign', 'sends'));
    }

    public function leads(Request $request)
    {
        $courseIds = $this->ownedCourseIds();
        $userId = auth()->id();

        $query = Lead::query()
            ->where(function ($q) use ($userId, $courseIds) {
                $q->where('created_by', $userId)
                    ->orWhereIn('course_id', $courseIds);
            })
            ->with(['course', 'assignee', 'convertedUser']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('source')) {
            $query->where('source', 'like', '%'.$request->source.'%');
        }
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $leads = $query->latest()->paginate(20)->withQueryString();
        $courses = $this->owned(Course::query())->orderBy('title')->get(['id', 'title']);
        $counselors = $this->counselorsQuery()->orderBy('name')->get(['id', 'name', 'email']);

        return view('admin.marketing.leads', compact('leads', 'courses', 'counselors'));
    }

    public function storeLead(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180'],
            'phone' => ['nullable', 'string', 'max:40'],
            'source' => ['nullable', 'string', 'max:80'],
            'course_id' => ['nullable', Rule::in($this->ownedCourseIds())],
            'assigned_to' => ['nullable', Rule::in($this->counselorsQuery()->pluck('id')->all())],
            'status' => ['nullable', 'in:new,contacted,qualified,converted,lost'],
            'stage' => ['nullable', 'in:new,contacted,counseling,documents,admitted,lost'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = $validated['status'] ?? 'new';
        $validated['stage'] = $validated['stage'] ?? 'new';
        $validated['source'] = $validated['source'] ?? 'manual';

        $lead = Lead::create($validated);

        ActivityLogger::log('lead_created', "Lead {$lead->name} captured", $lead);

        return back()->with('success', 'Lead captured.');
    }

    public function updateLead(Request $request, Lead $lead)
    {
        $this->authorizeLead($lead);

        $validated = $request->validate([
            'status' => ['nullable', 'in:new,contacted,qualified,converted,lost'],
            'stage' => ['nullable', 'in:new,contacted,counseling,documents,admitted,lost'],
            'assigned_to' => ['nullable', Rule::in($this->counselorsQuery()->pluck('id')->all())],
            'notes' => ['nullable', 'string'],
            'course_id' => ['nullable', Rule::in($this->ownedCourseIds())],
        ]);

        $lead->update($validated);

        return back()->with('success', 'Lead updated.');
    }

    public function assignLead(Request $request, Lead $lead)
    {
        $this->authorizeLead($lead);

        $validated = $request->validate([
            'assigned_to' => ['required', Rule::in($this->counselorsQuery()->pluck('id')->all())],
        ]);

        $lead->update([
            'assigned_to' => $validated['assigned_to'],
            'status' => $lead->status === 'new' ? 'contacted' : $lead->status,
            'stage' => $lead->stage === 'new' ? 'contacted' : $lead->stage,
        ]);

        ActivityLogger::log('lead_assigned', "Lead {$lead->name} assigned", $lead);

        return back()->with('success', 'Lead assigned to counselor.');
    }

    public function convertLead(Request $request, Lead $lead)
    {
        $this->authorizeLead($lead);

        $validated = $request->validate([
            'course_id' => ['nullable', Rule::in($this->ownedCourseIds())],
        ]);

        $user = $this->conversions->convert($lead, $validated['course_id'] ?? null, auth()->user());

        return back()->with('success', "Lead converted to learner ({$user->email}).");
    }

    protected function counselorsQuery()
    {
        return User::query()
            ->whereHas('role', fn ($q) => $q->whereIn('slug', ['counselor', 'admin', 'sub-admin']))
            ->where(function ($q) {
                $q->where('id', auth()->id())
                    ->orWhere('created_by', auth()->id());
            });
    }

    protected function authorizeLead(Lead $lead): void
    {
        $userId = (int) auth()->id();
        if ((int) $lead->created_by === $userId) {
            return;
        }
        if ($lead->course_id && $this->ownedCourseIds()->contains($lead->course_id)) {
            return;
        }
        abort(403, 'You do not have access to this lead.');
    }
}
