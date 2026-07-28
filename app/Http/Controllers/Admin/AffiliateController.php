<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\AffiliatePayout;
use App\Models\Setting;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\AffiliateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AffiliateController extends Controller
{
    use ScopesToCurrentUser;

    public function __construct(protected AffiliateService $affiliates) {}

    public function index(Request $request)
    {
        $query = $this->owned(Affiliate::query())
            ->with(['user'])
            ->withCount(['links', 'commissions', 'payouts'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $affiliates = $query->paginate(20)->withQueryString();

        $base = $this->owned(Affiliate::query());
        $stats = [
            'total' => (clone $base)->count(),
            'approved' => (clone $base)->where('status', 'approved')->count(),
            'pending' => (clone $base)->where('status', 'pending')->count(),
            'total_sales' => (clone $base)->sum('total_sales'),
            'total_commission' => (clone $base)->sum('total_commission'),
            'paid_commission' => (clone $base)->sum('paid_commission'),
        ];

        return view('admin.affiliates.index', compact('affiliates', 'stats'));
    }

    public function create()
    {
        $learners = $this->visibleLearnersQuery()->orderBy('name')->get();

        return view('admin.affiliates.create', compact('learners'));
    }

    public function store(Request $request)
    {
        $ownedLearnerIds = $this->visibleLearnersQuery()->pluck('id');

        $validated = $request->validate([
            'user_id' => ['nullable', Rule::in($ownedLearnerIds)],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'code' => ['nullable', 'string', 'max:40', 'unique:affiliates,code'],
            'commission_type' => ['required', 'in:percent,fixed'],
            'commission_value' => ['required', 'numeric', 'min:0'],
            'payment_details' => ['nullable', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if (! empty($validated['user_id'])) {
            $learner = User::find($validated['user_id']);
            $validated['name'] = $validated['name'] ?: $learner?->name;
            $validated['email'] = $validated['email'] ?: $learner?->email;
        }

        $affiliate = $this->affiliates->createAffiliate($validated, Auth::id());

        return redirect()
            ->route('admin.affiliates.show', $affiliate)
            ->with('success', 'Affiliate registered successfully.');
    }

    public function show(Affiliate $affiliate)
    {
        $this->authorizeOwner($affiliate);
        $affiliate->load(['user', 'links', 'commissions.order', 'payouts']);

        $links = $affiliate->links()->latest()->get();
        $commissions = $affiliate->commissions()->with('order', 'link')->latest()->paginate(15, ['*'], 'commissions_page');
        $payouts = $affiliate->payouts()->latest()->paginate(10, ['*'], 'payouts_page');

        return view('admin.affiliates.show', compact('affiliate', 'links', 'commissions', 'payouts'));
    }

    public function approve(Affiliate $affiliate)
    {
        $this->authorizeOwner($affiliate);
        $this->affiliates->approveAffiliate($affiliate);

        return back()->with('success', 'Affiliate approved.');
    }

    public function reject(Request $request, Affiliate $affiliate)
    {
        $this->authorizeOwner($affiliate);
        $validated = $request->validate(['notes' => ['nullable', 'string', 'max:1000']]);
        $this->affiliates->rejectAffiliate($affiliate, $validated['notes'] ?? null);

        return back()->with('success', 'Affiliate rejected.');
    }

    public function suspend(Request $request, Affiliate $affiliate)
    {
        $this->authorizeOwner($affiliate);
        $validated = $request->validate(['notes' => ['nullable', 'string', 'max:1000']]);
        $this->affiliates->suspendAffiliate($affiliate, $validated['notes'] ?? null);

        return back()->with('success', 'Affiliate suspended.');
    }

    public function storeLink(Request $request, Affiliate $affiliate)
    {
        $this->authorizeOwner($affiliate);

        $validated = $request->validate([
            'product_type' => ['required', 'in:course,bundle,custom'],
            'product_id' => ['nullable', 'integer', 'min:1'],
            'url_path' => ['nullable', 'string', 'max:255'],
        ]);

        $this->affiliates->getOrCreateLink(
            $affiliate,
            $validated['product_type'],
            $validated['product_id'] ?? null,
            Auth::id(),
            $validated['url_path'] ?? null
        );

        return back()->with('success', 'Affiliate link ready.');
    }

    public function storePayout(Request $request, Affiliate $affiliate)
    {
        $this->authorizeOwner($affiliate);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->affiliates->requestPayout(
            $affiliate,
            (float) $validated['amount'],
            Auth::id(),
            $validated['notes'] ?? null
        );

        return back()->with('success', 'Payout request recorded.');
    }

    public function markPayoutPaid(Request $request, AffiliatePayout $payout)
    {
        $this->authorizeOwner($payout);

        $validated = $request->validate([
            'payment_reference' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->affiliates->processPayout(
            $payout,
            'paid',
            $validated['payment_reference'] ?? null,
            $validated['notes'] ?? null
        );

        return back()->with('success', 'Payout marked as paid.');
    }

    public function settings()
    {
        $settings = Setting::where('group', 'affiliate')->get()->pluck('value', 'key');

        return view('admin.affiliates.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'enabled' => ['nullable', 'boolean'],
            'default_commission_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'cookie_days' => ['required', 'integer', 'min:1', 'max:365'],
            'auto_approve' => ['nullable', 'boolean'],
        ]);

        Setting::set('enabled', $request->boolean('enabled') ? '1' : '0', 'affiliate');
        Setting::set('default_commission_percent', (string) $validated['default_commission_percent'], 'affiliate');
        Setting::set('cookie_days', (string) $validated['cookie_days'], 'affiliate');
        Setting::set('auto_approve', $request->boolean('auto_approve') ? '1' : '0', 'affiliate');

        ActivityLogger::log('affiliate_settings_updated', 'Affiliate settings updated');

        return back()->with('success', 'Affiliate settings saved.');
    }
}
