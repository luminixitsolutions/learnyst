<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ExportsReportCsv;
use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Lead;
use App\Models\MockTest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ScheduledEvent;
use App\Models\TestSeries;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InsightController extends Controller
{
    use ExportsReportCsv;

    protected function learnerRoleId(): ?int
    {
        return DB::table('roles')->where('slug', 'learner')->value('id');
    }

    protected function dateBounds(Request $request): array
    {
        return [
            $request->get('from', now()->startOfMonth()->format('Y-m-d')),
            $request->get('to', now()->format('Y-m-d')),
        ];
    }

    protected function vitalsSummary(): array
    {
        $learnerRoleId = $this->learnerRoleId();
        $monthStart = now()->startOfMonth();

        $monthlyRevenue = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', $monthStart)
            ->sum('total');

        $activeLearners = User::where('role_id', $learnerRoleId)
            ->where(function ($q) use ($monthStart) {
                $q->where('last_login_at', '>=', $monthStart)
                    ->orWhereHas('enrollments', fn ($e) => $e->where('status', 'active'));
            })
            ->count();

        $totalLeads = Lead::where('created_at', '>=', $monthStart)->count();
        $convertedLeads = Lead::where('status', 'converted')->where('updated_at', '>=', $monthStart)->count();
        $conversions = $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 1) : 0;

        $avgProgress = CourseEnrollment::where('status', 'active')->avg('progress') ?? 0;
        $timeSpent = round($avgProgress * 0.6, 1) . ' hrs avg';

        return compact('monthlyRevenue', 'activeLearners', 'conversions', 'timeSpent');
    }

    protected function revenueChartData(string $from, string $to, string $period): Collection
    {
        $query = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$from, $to . ' 23:59:59']);

        return match ($period) {
            'weeks' => $query->selectRaw('YEARWEEK(created_at) as label, SUM(total) as value')
                ->groupBy('label')->orderBy('label')->get(),
            'months' => $query->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as label, SUM(total) as value')
                ->groupBy('label')->orderBy('label')->get(),
            default => $query->selectRaw('DATE(created_at) as label, SUM(total) as value')
                ->groupBy('label')->orderBy('label')->get(),
        };
    }

    protected function learnerChartData(string $from, string $to, string $period): Collection
    {
        $learnerRoleId = $this->learnerRoleId();
        $query = User::where('role_id', $learnerRoleId)
            ->whereBetween('created_at', [$from, $to . ' 23:59:59']);

        return match ($period) {
            'weeks' => $query->selectRaw('YEARWEEK(created_at) as label, COUNT(*) as value')
                ->groupBy('label')->orderBy('label')->get(),
            'months' => $query->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as label, COUNT(*) as value')
                ->groupBy('label')->orderBy('label')->get(),
            default => $query->selectRaw('DATE(created_at) as label, COUNT(*) as value')
                ->groupBy('label')->orderBy('label')->get(),
        };
    }

    public function dashboard()
    {
        $vitals = $this->vitalsSummary();

        return view('admin.insights.dashboard', $vitals);
    }

    public function schoolVitals()
    {
        $vitals = $this->vitalsSummary();

        return view('admin.insights.school-vitals', $vitals);
    }

    public function monthlyRevenue(Request $request)
    {
        [$from, $to] = $this->dateBounds($request);
        $period = $request->get('period', 'days');

        $orders = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$from, $to . ' 23:59:59'])
            ->get();

        $totals = $orders->groupBy(fn ($o) => $o->created_at->format('Y-m-d'))->map->sum('total');
        $summary = [
            'total' => $orders->sum('total'),
            'average' => $totals->count() ? round($totals->avg(), 2) : 0,
            'highest' => $totals->max() ?? 0,
            'lowest' => $totals->count() ? $totals->min() : 0,
        ];

        $chartData = $this->revenueChartData($from, $to, $period);

        if ($request->boolean('export')) {
            return $this->exportCsv('monthly-revenue', ['Date', 'Revenue'], $chartData->map(fn ($r) => [$r->label, $r->value]));
        }

        return view('admin.insights.monthly-revenue', compact('from', 'to', 'period', 'summary', 'chartData'));
    }

    public function activeLearners(Request $request)
    {
        [$from, $to] = $this->dateBounds($request);
        $period = $request->get('period', 'days');
        $learnerRoleId = $this->learnerRoleId();

        $signups = User::where('role_id', $learnerRoleId)
            ->whereBetween('created_at', [$from, $to . ' 23:59:59'])
            ->count();

        $activeCount = User::where('role_id', $learnerRoleId)
            ->whereBetween('last_login_at', [$from, $to . ' 23:59:59'])
            ->count();

        $chartData = $this->learnerChartData($from, $to, $period);

        if ($request->boolean('export')) {
            return $this->exportCsv('active-learners', ['Period', 'Signups'], $chartData->map(fn ($r) => [$r->label, $r->value]));
        }

        return view('admin.insights.active-learners', compact('from', 'to', 'period', 'signups', 'activeCount', 'chartData'));
    }

    public function conversions(Request $request)
    {
        [$from, $to] = $this->dateBounds($request);
        $period = $request->get('period', 'days');

        $paidEnrollments = CourseEnrollment::where('access_type', 'paid')
            ->whereBetween('enrolled_at', [$from, $to . ' 23:59:59'])
            ->count();

        $chartData = CourseEnrollment::where('access_type', 'paid')
            ->whereBetween('enrolled_at', [$from, $to . ' 23:59:59'])
            ->selectRaw(match ($period) {
                'weeks' => 'YEARWEEK(enrolled_at) as label, COUNT(*) as value',
                'months' => 'DATE_FORMAT(enrolled_at, "%Y-%m") as label, COUNT(*) as value',
                default => 'DATE(enrolled_at) as label, COUNT(*) as value',
            })
            ->groupBy('label')
            ->orderBy('label')
            ->get();

        if ($request->boolean('export')) {
            return $this->exportCsv('conversions', ['Period', 'Paid Enrollments'], $chartData->map(fn ($r) => [$r->label, $r->value]));
        }

        return view('admin.insights.conversions', compact('from', 'to', 'period', 'paidEnrollments', 'chartData'));
    }

    public function timeSpent(Request $request)
    {
        [$from, $to] = $this->dateBounds($request);
        $period = $request->get('period', 'days');
        $learnerRoleId = $this->learnerRoleId();

        $summary = [
            'average' => round(CourseEnrollment::avg('progress') ?? 0, 1),
            'learners' => User::where('role_id', $learnerRoleId)->where('last_login_at', '>=', $from)->count(),
            'courses' => Course::count(),
            'tests' => MockTest::count(),
            'test_series' => TestSeries::count(),
            'bundles' => DB::table('bundles')->count(),
            'newsfeed' => 0,
            'communities' => DB::table('communities')->count(),
        ];

        $chartData = CourseEnrollment::whereBetween('updated_at', [$from, $to . ' 23:59:59'])
            ->selectRaw(match ($period) {
                'weeks' => 'YEARWEEK(updated_at) as label, AVG(progress) as value',
                'months' => 'DATE_FORMAT(updated_at, "%Y-%m") as label, AVG(progress) as value',
                default => 'DATE(updated_at) as label, AVG(progress) as value',
            })
            ->groupBy('label')
            ->orderBy('label')
            ->get();

        if ($request->boolean('export')) {
            return $this->exportCsv('time-spent', ['Period', 'Avg Progress %'], $chartData->map(fn ($r) => [$r->label, round($r->value, 1)]));
        }

        return view('admin.insights.time-spent', compact('from', 'to', 'period', 'summary', 'chartData'));
    }

    public function salesIndex()
    {
        return view('admin.insights.sales.index');
    }

    protected function trialQuery(Request $request, string $accessType)
    {
        $query = CourseEnrollment::with(['user', 'course'])
            ->where('access_type', $accessType);

        if ($search = $request->get('search')) {
            $query->whereHas('user', fn ($u) => $u->where('email', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%"));
        }

        if ($lastAccess = $request->get('last_access')) {
            $query->whereHas('user', fn ($u) => $u->whereDate('last_login_at', $lastAccess));
        }

        return $query->latest('enrolled_at');
    }

    public function freshTrial(Request $request)
    {
        $records = $this->trialQuery($request, 'trial')->paginate(30)->withQueryString();

        if ($request->boolean('export')) {
            return $this->exportCsv('fresh-trial', ['Learner', 'Email', 'Product', 'Enrolled', 'Last Access', 'Status'],
                $records->getCollection()->map(fn ($e) => [
                    $e->user?->name, $e->user?->email, $e->course?->title,
                    $e->enrolled_at?->format('Y-m-d'), $e->user?->last_login_at?->format('Y-m-d'), $e->status,
                ]));
        }

        return view('admin.insights.sales.fresh-trial', compact('records'));
    }

    public function upsellTrial(Request $request)
    {
        $records = $this->trialQuery($request, 'trial')
            ->whereHas('user.orders', fn ($q) => $q->where('payment_status', 'paid'))
            ->paginate(30)->withQueryString();

        if ($request->boolean('export')) {
            return $this->exportCsv('upsell-trial', ['Learner', 'Email', 'Trial Product', 'Purchased', 'Last Access', 'Status'],
                $records->getCollection()->map(fn ($e) => [
                    $e->user?->name, $e->user?->email, $e->course?->title,
                    $e->user?->orders()->where('payment_status', 'paid')->latest()->first()?->order_number,
                    $e->user?->last_login_at?->format('Y-m-d'), $e->status,
                ]));
        }

        return view('admin.insights.sales.upsell-trial', compact('records'));
    }

    public function renewalTrial(Request $request)
    {
        $records = CourseEnrollment::with(['user', 'course'])
            ->whereNotNull('expires_at')
            ->when($request->search, fn ($q, $s) => $q->whereHas('user', fn ($u) => $u->where('email', 'like', "%{$s}%")))
            ->when($request->last_access, fn ($q, $d) => $q->whereHas('user', fn ($u) => $u->whereDate('last_login_at', $d)))
            ->latest('expires_at')
            ->paginate(30)->withQueryString();

        if ($request->boolean('export')) {
            return $this->exportCsv('renewal-trial', ['Learner', 'Email', 'Product', 'Expiry', 'Last Access', 'Renewal Status'],
                $records->getCollection()->map(fn ($e) => [
                    $e->user?->name, $e->user?->email, $e->course?->title,
                    $e->expires_at?->format('Y-m-d'), $e->user?->last_login_at?->format('Y-m-d'),
                    $e->expires_at?->isPast() ? 'Expired' : 'Active',
                ]));
        }

        return view('admin.insights.sales.renewal-trial', compact('records'));
    }

    public function freeUsers(Request $request)
    {
        $records = $this->trialQuery($request, 'free')->paginate(30)->withQueryString();

        if ($request->boolean('export')) {
            return $this->exportCsv('free-users', ['Learner', 'Email', 'Free Product', 'Enrolled', 'Last Access', 'Upsell Status'],
                $records->getCollection()->map(fn ($e) => [
                    $e->user?->name, $e->user?->email, $e->course?->title,
                    $e->enrolled_at?->format('Y-m-d'), $e->user?->last_login_at?->format('Y-m-d'),
                    $e->user?->orders()->where('payment_status', 'paid')->exists() ? 'Upsold' : 'Pending',
                ]));
        }

        return view('admin.insights.sales.free-users', compact('records'));
    }

    public function liveIndex()
    {
        return view('admin.insights.live.index');
    }

    public function liveClasses(Request $request)
    {
        $records = ScheduledEvent::with(['course', 'instructor'])
            ->when($request->search, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->latest('starts_at')
            ->paginate(30)->withQueryString();

        if ($request->boolean('export')) {
            return $this->exportCsv('live-classes', ['Class', 'Instructor', 'Start', 'End', 'Status'],
                $records->getCollection()->map(fn ($e) => [
                    $e->title, $e->instructor?->name, $e->starts_at?->format('Y-m-d H:i'), $e->ends_at?->format('Y-m-d H:i'), $e->status,
                ]));
        }

        return view('admin.insights.live.classes', compact('records'));
    }

    public function checkout(Request $request)
    {
        [$from, $to] = $this->dateBounds($request);

        $stats = [
            'total' => Order::whereBetween('created_at', [$from, $to . ' 23:59:59'])->count(),
            'paid' => Order::where('payment_status', 'paid')->whereBetween('created_at', [$from, $to . ' 23:59:59'])->count(),
            'pending' => Order::where('payment_status', 'pending')->whereBetween('created_at', [$from, $to . ' 23:59:59'])->count(),
            'failed' => Payment::where('status', 'failed')->whereBetween('created_at', [$from, $to . ' 23:59:59'])->count(),
        ];

        $stats['abandoned'] = max($stats['total'] - $stats['paid'] - $stats['pending'], 0);
        $stats['conversion'] = $stats['total'] > 0 ? round(($stats['paid'] / $stats['total']) * 100, 1) : 0;

        return view('admin.insights.live.checkout', compact('from', 'to', 'stats'));
    }

    public function testTakes(Request $request)
    {
        $records = CourseEnrollment::with(['user', 'course'])
            ->where(function ($q) {
                $q->whereNotNull('meta->mock_test_score')->orWhereNotNull('meta->test_series_score');
            })
            ->when($request->search, fn ($q, $s) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$s}%")))
            ->latest('updated_at')
            ->paginate(30)->withQueryString();

        if ($request->boolean('export')) {
            return $this->exportCsv('test-takes', ['Learner', 'Test', 'Score', 'Attempt Date', 'Status'],
                $records->getCollection()->map(fn ($e) => [
                    $e->user?->name,
                    $e->course?->title,
                    $e->meta['mock_test_score'] ?? $e->meta['test_series_score'] ?? '—',
                    $e->updated_at?->format('Y-m-d'),
                    $e->status,
                ]));
        }

        return view('admin.insights.live.test-takes', compact('records'));
    }

    public function marketingIndex()
    {
        return view('admin.insights.marketing.index');
    }

    public function ctaInsights(Request $request)
    {
        $records = Lead::with('course')
            ->when($request->search, fn ($q, $s) => $q->where('source', 'like', "%{$s}%")->orWhere('name', 'like', "%{$s}%"))
            ->latest()
            ->paginate(30)->withQueryString();

        if ($request->boolean('export')) {
            return $this->exportCsv('cta-insights', ['CTA/Source', 'Page', 'Clicks', 'Views', 'Conversion', 'Last Date'],
                $records->getCollection()->map(fn ($l) => [
                    $l->source ?? 'Direct', $l->course?->title ?? 'Homepage', 1, 1,
                    $l->status === 'converted' ? '100%' : '0%', $l->updated_at->format('Y-m-d'),
                ]));
        }

        return view('admin.insights.marketing.cta', compact('records'));
    }

    public function messengerIndex()
    {
        return view('admin.insights.messenger.index');
    }

    protected function campaignQuery(Request $request, ?string $channel = null)
    {
        return Campaign::query()
            ->when($channel, fn ($q) => $q->where('channel', $channel))
            ->when($request->search, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->latest();
    }

    public function systemMails(Request $request)
    {
        $records = collect();

        if ($request->boolean('export')) {
            return $this->exportCsv('system-mails', ['Email', 'Trigger', 'Sent', 'Opens', 'Clicks', 'Failed', 'Date'], $records);
        }

        return view('admin.insights.messenger.system-mails', compact('records'));
    }

    public function marketingMails(Request $request)
    {
        $records = $this->campaignQuery($request, 'email')->paginate(30)->withQueryString();

        if ($request->boolean('export')) {
            return $this->exportCsv('marketing-mails', ['Campaign', 'Sent', 'Opens', 'Clicks', 'Conversions', 'Status', 'Date'],
                $records->getCollection()->map(fn ($c) => [
                    $c->title, '—', '—', '—', '—', $c->status, ($c->sent_at ?? $c->scheduled_at)?->format('Y-m-d'),
                ]));
        }

        return view('admin.insights.messenger.marketing-mails', compact('records'));
    }

    public function pushMessages(Request $request)
    {
        $records = collect();

        if ($request->boolean('export')) {
            return $this->exportCsv('push-messages', ['Title', 'Sent', 'Delivered', 'Opens', 'Clicks', 'Status', 'Date'], $records);
        }

        return view('admin.insights.messenger.push-messages', compact('records'));
    }

    public function workflowMails(Request $request)
    {
        $records = collect();

        if ($request->boolean('export')) {
            return $this->exportCsv('workflow-mails', ['Workflow', 'Email', 'Sent', 'Opens', 'Clicks', 'Failed', 'Status'], $records);
        }

        return view('admin.insights.messenger.workflow-mails', compact('records'));
    }

    public function emailDelivery(Request $request)
    {
        $records = collect();

        if ($request->boolean('export')) {
            return $this->exportCsv('email-delivery', ['Email', 'Delivered', 'Opened', 'Clicked', 'Bounced', 'Failed', 'Date'], $records);
        }

        return view('admin.insights.messenger.email-delivery', compact('records'));
    }

    public function bouncesComplaints(Request $request)
    {
        $records = collect();

        if ($request->boolean('export')) {
            return $this->exportCsv('bounces-complaints', ['Email', 'Bounce Reason', 'Complaint', 'Type', 'Date', 'Status'], $records);
        }

        return view('admin.insights.messenger.bounces-complaints', compact('records'));
    }

    public function whatsappMessages(Request $request)
    {
        $records = $this->campaignQuery($request, 'whatsapp')->paginate(30)->withQueryString();

        if ($request->boolean('export')) {
            return $this->exportCsv('whatsapp-messages', ['Campaign', 'Sent', 'Delivered', 'Read', 'Failed', 'Date', 'Status'],
                $records->getCollection()->map(fn ($c) => [
                    $c->title, '—', '—', '—', '—', ($c->sent_at ?? $c->scheduled_at)?->format('Y-m-d'), $c->status,
                ]));
        }

        return view('admin.insights.messenger.whatsapp-messages', compact('records'));
    }

    public function whatsappWorkflow(Request $request)
    {
        $records = collect();

        if ($request->boolean('export')) {
            return $this->exportCsv('whatsapp-workflow', ['Workflow', 'Template', 'Sent', 'Delivered', 'Read', 'Failed', 'Status'], $records);
        }

        return view('admin.insights.messenger.whatsapp-workflow', compact('records'));
    }
}
