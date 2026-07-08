<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ExportsReportCsv;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Bundle;
use App\Models\Campaign;
use App\Models\Certificate;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CustomProduct;
use App\Models\Ebook;
use App\Models\Lead;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Podcast;
use App\Models\Resource;
use App\Models\ResourceDownload;
use App\Models\ScheduledEvent;
use App\Models\Setting;
use App\Models\User;
use App\Models\Webinar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    use ExportsReportCsv;

    protected function learnerRoleId(): ?int
    {
        return DB::table('roles')->where('slug', 'learner')->value('id');
    }

    protected function enrollmentSearch($query, ?string $search)
    {
        if (! $search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%"))
                ->orWhereHas('course', fn ($c) => $c->where('title', 'like', "%{$search}%"))
                ->orWhereHas('bundle', fn ($b) => $b->where('title', 'like', "%{$search}%"));
        });
    }

    protected function paymentSearch($query, ?string $search)
    {
        if (! $search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('transaction_id', 'like', "%{$search}%")
                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                ->orWhereHas('order', fn ($o) => $o->where('order_number', 'like', "%{$search}%"));
        });
    }

    protected function dateRange($query, Request $request, string $column = 'created_at')
    {
        $from = $request->get('from');
        $to = $request->get('to');

        if ($from) {
            $query->where($column, '>=', $from);
        }
        if ($to) {
            $query->where($column, '<=', $to . ' 23:59:59');
        }

        return $query;
    }

    public function index()
    {
        return view('admin.reports.index');
    }

    public function salesIndex()
    {
        return view('admin.reports.sales-index');
    }

    public function progressIndex()
    {
        return view('admin.reports.progress-index');
    }

    public function sales(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->get('to', now()->format('Y-m-d'));

        $query = Order::with('user')
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$from, $to . ' 23:59:59']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $orders = $query->latest()->get();
        $total = $orders->sum('total');

        if ($request->boolean('export')) {
            return $this->exportCsv('sales', ['Order', 'Customer', 'Email', 'Total', 'Date'], $orders->map(fn ($o) => [
                $o->order_number,
                $o->user?->name,
                $o->user?->email,
                $o->total,
                $o->created_at->format('Y-m-d H:i'),
            ]));
        }

        return view('admin.reports.sales', compact('orders', 'total', 'from', 'to'));
    }

    public function productSales(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->get('to', now()->format('Y-m-d'));

        $query = OrderItem::with(['order.user', 'order.coupon', 'course'])
            ->whereHas('order', function ($q) use ($from, $to, $request) {
                $q->whereBetween('created_at', [$from, $to . ' 23:59:59']);
                if ($search = $request->get('search')) {
                    $q->where(function ($sq) use ($search) {
                        $sq->where('order_number', 'like', "%{$search}%")
                            ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
                    });
                }
                if ($status = $request->get('status')) {
                    $q->where('payment_status', $status);
                }
            });

        $items = $query->latest()->paginate(30)->withQueryString();

        if ($request->boolean('export')) {
            $rows = $items->getCollection()->map(fn ($item) => [
                $item->course?->title,
                $item->order?->order_number,
                $item->order?->user?->name,
                $item->total,
                $item->discount,
                $item->order?->coupon?->code,
                $item->order?->payment_status,
                $item->order?->created_at?->format('Y-m-d'),
            ]);

            return $this->exportCsv('product-sales', [
                'Product', 'Order', 'Learner', 'Amount', 'Discount', 'Coupon', 'Payment Status', 'Date',
            ], $rows);
        }

        return view('admin.reports.product-sales', compact('items', 'from', 'to'));
    }

    public function learners(Request $request)
    {
        $query = User::where('role_id', $this->learnerRoleId())
            ->withCount(['enrollments', 'orders'])
            ->with('segments');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $learners = $query->orderByDesc('total_spent')->paginate(30)->withQueryString();

        $leadCounts = Lead::select('email', DB::raw('count(*) as visits'))
            ->groupBy('email')
            ->pluck('visits', 'email');

        if ($request->boolean('export')) {
            $rows = $learners->getCollection()->map(fn ($l) => [
                $l->name,
                $l->email,
                $l->phone,
                $l->total_spent,
                $leadCounts[$l->email] ?? 0,
                $l->created_at->format('Y-m-d'),
                $l->address,
            ]);

            return $this->exportCsv('learners', [
                'Name', 'Email', 'Phone', 'Total Sales', 'Lead Visits', 'Signed Up On', 'Billing Address',
            ], $rows);
        }

        return view('admin.reports.learners', compact('learners', 'leadCounts'));
    }

    public function learnerProfile(User $user)
    {
        $user->load(['enrollments.course', 'certificates.course', 'segments', 'orders']);

        $courses = Course::where('status', 'published')->orderBy('title')->get(['id', 'title']);
        $pendingPayments = Payment::where('user_id', $user->id)->whereIn('status', ['pending', 'failed'])->latest()->get();

        return view('admin.reports.learner-profile', compact('user', 'courses', 'pendingPayments'));
    }

    public function courses(Request $request)
    {
        $courses = Course::withCount('enrollments')
            ->withAvg('enrollments as avg_progress', 'progress')
            ->when($request->search, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $st) => $q->where('status', $st))
            ->orderByDesc('enrollments_count')
            ->paginate(20)
            ->withQueryString();

        if ($request->boolean('export')) {
            return $this->exportCsv('courses', ['Course', 'Enrollments', 'Avg Progress', 'Status'], $courses->map(fn ($c) => [
                $c->title,
                $c->enrollments_count,
                round($c->avg_progress ?? 0) . '%',
                $c->status,
            ]));
        }

        return view('admin.reports.courses', compact('courses'));
    }

    public function enrollments(Request $request)
    {
        $query = CourseEnrollment::with(['user', 'course', 'batch', 'bundle']);
        $query = $this->enrollmentSearch($query, $request->get('search'));
        $query = $this->dateRange($query, $request, 'enrolled_at');

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        if ($type = $request->get('type')) {
            $query->where('enrollment_type', $type);
        }
        if ($courseId = $request->get('course_id')) {
            $query->where('course_id', $courseId);
        }

        $enrollments = $query->latest('enrolled_at')->paginate(30)->withQueryString();
        $courses = Course::orderBy('title')->get(['id', 'title']);

        if ($request->boolean('export')) {
            return $this->exportCsv('enrollments', [
                'Product/Course', 'Learner', 'Email', 'Mobile', 'Enrollment Date', 'Access Start', 'Access Expiry', 'Status',
            ], $enrollments->getCollection()->map(fn ($e) => [
                $e->course?->title ?? $e->bundle?->title ?? $e->batch?->title,
                $e->user?->name,
                $e->user?->email,
                $e->user?->phone,
                $e->enrolled_at?->format('Y-m-d'),
                $e->access_starts_at?->format('Y-m-d'),
                $e->expires_at?->format('Y-m-d'),
                $e->status,
            ]));
        }

        return view('admin.reports.enrollments', compact('enrollments', 'courses'));
    }

    public function transactions(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->get('to', now()->format('Y-m-d'));

        $query = Payment::with(['user', 'order.items.course']);
        $query = $this->paymentSearch($query, $request->get('search'));

        if ($request->filled('from')) {
            $query->where('created_at', '>=', $from);
        }
        if ($request->filled('to')) {
            $query->where('created_at', '<=', $to . ' 23:59:59');
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        if ($gateway = $request->get('gateway')) {
            $query->where('gateway', $gateway);
        }

        $payments = $query->latest()->paginate(30)->withQueryString();

        if ($request->boolean('export')) {
            return $this->exportCsv('transactions', [
                'Order ID', 'Learner', 'Product', 'Amount', 'Payment Mode', 'Payment Status', 'Transaction ID', 'Created Date',
            ], $payments->getCollection()->map(fn ($p) => [
                $p->order?->order_number,
                $p->user?->name,
                $p->order?->items->first()?->course?->title,
                $p->amount,
                $p->gateway,
                $p->status,
                $p->transaction_id,
                $p->created_at->format('Y-m-d H:i'),
            ]));
        }

        return view('admin.reports.transactions', compact('payments', 'from', 'to'));
    }

    public function payments(Request $request)
    {
        return $this->transactions($request);
    }

    public function paymentGateways(Request $request)
    {
        $gateways = Payment::select('gateway')
            ->selectRaw('COUNT(*) as transaction_count')
            ->selectRaw('SUM(CASE WHEN status = "success" THEN amount ELSE 0 END) as total_amount')
            ->selectRaw('MAX(updated_at) as last_updated')
            ->when($request->search, fn ($q, $s) => $q->where('gateway', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $st) => $q->where('status', $st))
            ->groupBy('gateway')
            ->get()
            ->map(function ($row) {
                $setting = Setting::where('group', 'payment')->where('key', $row->gateway . '_enabled')->first();

                return (object) [
                    'name' => ucfirst($row->gateway),
                    'slug' => $row->gateway,
                    'updated_by' => 'System',
                    'config_type' => $setting ? 'Configured' : 'Default',
                    'status' => $setting?->value === '1' || $setting?->value === 'true' ? 'Active' : 'Active',
                    'transaction_count' => $row->transaction_count,
                    'total_amount' => $row->total_amount,
                    'last_updated' => $row->last_updated,
                ];
            });

        if ($request->boolean('export')) {
            return $this->exportCsv('payment-gateways', [
                'Gateway', 'Updated By', 'Configuration Type', 'Status', 'Transactions', 'Total Amount',
            ], $gateways->map(fn ($g) => [
                $g->name, $g->updated_by, $g->config_type, $g->status, $g->transaction_count, $g->total_amount,
            ]));
        }

        return view('admin.reports.payment-gateways', compact('gateways'));
    }

    public function schoolPayouts(Request $request)
    {
        $payouts = collect();

        if ($request->boolean('export')) {
            return $this->exportCsv('school-payouts', [
                'Payout ID', 'Transaction ID', 'Amount', 'Payment Gateway', 'Status', 'Date',
            ], $payouts);
        }

        return view('admin.reports.school-payouts', compact('payouts'));
    }

    public function productProgress(Request $request, string $type)
    {
        $types = $this->progressTypes();
        abort_unless(isset($types[$type]), 404);

        $metaKey = $types[$type]['meta_key'] ?? null;
        $query = CourseEnrollment::with(['user', 'course'])
            ->where('enrollment_type', 'course');

        $query = $this->enrollmentSearch($query, $request->get('search'));
        $query = $this->dateRange($query, $request, 'enrolled_at');

        if ($courseId = $request->get('course_id')) {
            $query->where('course_id', $courseId);
        }
        if ($learnerId = $request->get('learner_id')) {
            $query->where('user_id', $learnerId);
        }
        if ($metaKey) {
            $query->whereNotNull('meta->' . $metaKey);
        }

        $records = $query->latest('updated_at')->paginate(30)->withQueryString();
        $courses = Course::orderBy('title')->get(['id', 'title']);
        $learners = User::where('role_id', $this->learnerRoleId())->orderBy('name')->get(['id', 'name']);
        $reportType = $types[$type];

        if ($request->boolean('export')) {
            return $this->exportCsv($type . '-progress', [
                'Product', 'Learner', 'Progress %', 'Completed Lessons', 'Total Lessons', 'Last Activity', 'Status',
            ], $records->getCollection()->map(function ($e) use ($metaKey) {
                $meta = $e->meta ?? [];

                return [
                    $e->course?->title,
                    $e->user?->name,
                    ($e->progress ?? 0) . '%',
                    $meta['completed_lessons'] ?? '—',
                    $meta['total_lessons'] ?? '—',
                    $e->updated_at?->format('Y-m-d'),
                    $e->status,
                ];
            }));
        }

        return view('admin.reports.progress-type', compact('records', 'courses', 'learners', 'type', 'reportType'));
    }

    protected function progressTypes(): array
    {
        return [
            'ebook' => ['title' => 'Ebook Progress', 'meta_key' => null],
            'podcast' => ['title' => 'Podcast Progress', 'meta_key' => null],
            'custom-product' => ['title' => 'Custom Product Progress', 'meta_key' => null],
            'webinar' => ['title' => 'Webinar Progress', 'meta_key' => null],
            'digital-evaluation' => ['title' => 'Digital Evaluation', 'meta_key' => null],
            'course-quiz' => ['title' => 'Course Quiz Scores', 'meta_key' => 'course_quiz_score'],
            'mock-test' => ['title' => 'Mock Test Scores', 'meta_key' => 'mock_test_score'],
            'test-series' => ['title' => 'Test Series Scores', 'meta_key' => 'test_series_score'],
            'bundle-quiz' => ['title' => 'Bundle Quiz Score', 'meta_key' => 'bundle_quiz_score'],
            'code-submission' => ['title' => 'Code Submission Area', 'meta_key' => null],
            'quiz-insights' => ['title' => 'Quiz Insights', 'meta_key' => 'course_quiz_score'],
        ];
    }

    public function bundleProgress(Request $request)
    {
        $query = CourseEnrollment::with(['user', 'bundle'])
            ->where('enrollment_type', 'bundle')
            ->whereNotNull('bundle_id');

        $query = $this->enrollmentSearch($query, $request->get('search'));

        if ($bundleId = $request->get('bundle_id')) {
            $query->where('bundle_id', $bundleId);
        }

        $records = $query->latest('updated_at')->paginate(30)->withQueryString();
        $bundles = Bundle::orderBy('title')->get(['id', 'title']);

        if ($request->boolean('export')) {
            return $this->exportCsv('bundle-progress', [
                'Bundle', 'Learner', 'Email', 'Courses Completed', 'Total Courses', 'Progress %', 'Last Activity', 'Status',
            ], $records->getCollection()->map(function ($e) {
                $meta = $e->meta ?? [];

                return [
                    $e->bundle?->title,
                    $e->user?->name,
                    $e->user?->email,
                    $meta['courses_completed'] ?? '—',
                    $meta['total_courses'] ?? $e->bundle?->courses()->count(),
                    ($e->progress ?? 0) . '%',
                    $e->updated_at?->format('Y-m-d'),
                    $e->status,
                ];
            }));
        }

        return view('admin.reports.bundle-progress', compact('records', 'bundles'));
    }

    public function customProductProgress(Request $request)
    {
        $products = CustomProduct::orderBy('title')->get(['id', 'title']);
        $records = CourseEnrollment::with(['user', 'course'])
            ->where('enrollment_type', 'course')
            ->when($request->search, fn ($q, $s) => $this->enrollmentSearch($q, $s))
            ->latest('updated_at')
            ->paginate(30)
            ->withQueryString();

        if ($request->boolean('export')) {
            return $this->exportCsv('custom-product-progress', [
                'Product', 'Learner', 'Email', 'Progress', 'Completed Content', 'Last Activity', 'Status',
            ], $records->getCollection()->map(fn ($e) => [
                $e->course?->title,
                $e->user?->name,
                $e->user?->email,
                ($e->progress ?? 0) . '%',
                ($e->meta['completed_content'] ?? '—'),
                $e->updated_at?->format('Y-m-d'),
                $e->status,
            ]));
        }

        return view('admin.reports.custom-product-progress', compact('records', 'products'));
    }

    public function testSeriesScores(Request $request)
    {
        $query = CourseEnrollment::with(['user', 'course', 'bundle'])
            ->whereNotNull('meta->test_series_score');

        $query = $this->enrollmentSearch($query, $request->get('search'));
        $query = $this->dateRange($query, $request, 'updated_at');

        $records = $query->latest('updated_at')->paginate(30)->withQueryString();

        if ($request->boolean('export')) {
            return $this->exportCsv('test-series-scores', [
                'Learner', 'Email', 'Test Series', 'Score', 'Total Marks', 'Percentage', 'Attempt Date', 'Pass/Fail',
            ], $records->getCollection()->map(function ($e) {
                $score = $e->meta['test_series_score'] ?? 0;
                $total = $e->meta['test_series_total'] ?? 100;
                $pct = $total > 0 ? round(($score / $total) * 100, 1) : 0;

                return [
                    $e->user?->name,
                    $e->user?->email,
                    $e->course?->title ?? $e->bundle?->title,
                    $score,
                    $total,
                    $pct . '%',
                    $e->updated_at?->format('Y-m-d'),
                    $pct >= 40 ? 'Pass' : 'Fail',
                ];
            }));
        }

        return view('admin.reports.test-series-scores', compact('records'));
    }

    public function batches(Request $request)
    {
        $query = Batch::withCount('learners')->with(['course', 'instructor']);
        $query->when($request->search, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"));
        $query->when($request->status, fn ($q, $st) => $q->where('status', $st));

        $batches = $query->latest()->paginate(30)->withQueryString();

        if ($request->boolean('export')) {
            return $this->exportCsv('batches', [
                'Batch', 'Product/Course', 'Instructor', 'Start Date', 'End Date', 'Total Learners', 'Status',
            ], $batches->getCollection()->map(fn ($b) => [
                $b->title,
                $b->course?->title,
                $b->instructor?->name,
                $b->start_date?->format('Y-m-d'),
                $b->end_date?->format('Y-m-d'),
                $b->learners_count,
                $b->status,
            ]));
        }

        return view('admin.reports.batches', compact('batches'));
    }

    public function bundles(Request $request)
    {
        $bundles = Bundle::withCount(['courses', 'enrollments'])
            ->when($request->search, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->get();

        if ($request->boolean('export')) {
            return $this->exportCsv('bundles', ['Bundle', 'Status', 'Courses', 'Enrollments', 'Price'], $bundles->map(fn ($b) => [
                $b->title, $b->status, $b->courses_count, $b->enrollments_count, $b->price,
            ]));
        }

        return view('admin.reports.bundles', compact('bundles'));
    }

    public function referralWallet(Request $request)
    {
        $records = collect();

        if ($request->boolean('export')) {
            return $this->exportCsv('referral-wallet', [
                'Learner', 'Referral Code', 'Referred User', 'Wallet Amount', 'Credit/Debit', 'Transaction Type', 'Date', 'Status',
            ], $records);
        }

        return view('admin.reports.referral-wallet', compact('records'));
    }

    public function affiliateProducts(Request $request)
    {
        $records = collect();

        if ($request->boolean('export')) {
            return $this->exportCsv('affiliate-products', [
                'Affiliate', 'Product', 'Sales Count', 'Commission', 'Conversion', 'Payout Status', 'Date',
            ], $records);
        }

        return view('admin.reports.affiliate-products', compact('records'));
    }

    public function affiliates(Request $request)
    {
        $records = collect();

        if ($request->boolean('export')) {
            return $this->exportCsv('affiliates', [
                'Affiliate', 'Email', 'Total Referrals', 'Total Sales', 'Commission Earned', 'Commission Paid', 'Pending', 'Status',
            ], $records);
        }

        return view('admin.reports.affiliates', compact('records'));
    }

    public function coupons(Request $request)
    {
        $coupons = Coupon::query()
            ->when($request->search, fn ($q, $s) => $q->where('code', 'like', "%{$s}%"))
            ->when($request->status === 'active', fn ($q) => $q->where('is_active', true))
            ->when($request->status === 'inactive', fn ($q) => $q->where('is_active', false))
            ->orderByDesc('used_count')
            ->paginate(30)
            ->withQueryString();

        if ($request->boolean('export')) {
            return $this->exportCsv('coupons', [
                'Code', 'Discount Type', 'Discount Value', 'Usage Count', 'Total Discount', 'Start Date', 'End Date', 'Status',
            ], $coupons->getCollection()->map(fn ($c) => [
                $c->code,
                $c->discount_type,
                $c->discount_value,
                $c->used_count,
                Order::where('coupon_id', $c->id)->sum('discount'),
                $c->starts_at?->format('Y-m-d'),
                $c->expires_at?->format('Y-m-d'),
                $c->is_active ? 'Active' : 'Inactive',
            ]));
        }

        return view('admin.reports.coupons', compact('coupons'));
    }

    public function broadcastMessages(Request $request)
    {
        $campaigns = Campaign::query()
            ->when($request->search, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($request->channel, fn ($q, $ch) => $q->where('channel', $ch))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        if ($request->boolean('export')) {
            return $this->exportCsv('broadcast-messages', [
                'Campaign', 'Status', 'Scheduled', 'Sent At', 'Channel',
            ], $campaigns->getCollection()->map(fn ($c) => [
                $c->title,
                $c->status,
                $c->scheduled_at?->format('Y-m-d H:i'),
                $c->sent_at?->format('Y-m-d H:i'),
                ucfirst($c->channel ?? 'email'),
            ]));
        }

        return view('admin.reports.broadcast-messages', compact('campaigns'));
    }

    public function zoomInsights(Request $request)
    {
        $events = ScheduledEvent::with(['course', 'instructor'])
            ->where('platform', 'zoom')
            ->when($request->search, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->latest('starts_at')
            ->paginate(30)
            ->withQueryString();

        if ($request->boolean('export')) {
            return $this->exportCsv('zoom-insights', [
                'Zoom Class', 'Learner', 'Join Time', 'Leave Time', 'Duration', 'Attendance Status',
            ], collect());
        }

        return view('admin.reports.zoom-insights', compact('events'));
    }

    public function liveClassAttendance(Request $request)
    {
        $events = ScheduledEvent::with(['course', 'batch'])
            ->where('type', 'live_class')
            ->when($request->search, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->latest('starts_at')
            ->paginate(30)
            ->withQueryString();

        if ($request->boolean('export')) {
            return $this->exportCsv('live-class-attendance', [
                'Class', 'Learner', 'Email', 'Join Time', 'Leave Time', 'Duration', 'Status',
            ], collect());
        }

        return view('admin.reports.live-class-attendance', compact('events'));
    }

    public function resourceUsage(Request $request)
    {
        $query = ResourceDownload::with(['resource.category', 'user'])
            ->when($request->search, fn ($q, $s) => $q->whereHas('resource', fn ($r) => $r->where('title', 'like', "%{$s}%"))
                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$s}%")));

        $query = $this->dateRange($query, $request);

        $downloads = $query->latest()->paginate(30)->withQueryString();

        if ($request->boolean('export')) {
            return $this->exportCsv('resource-usage', [
                'Resource', 'Learner', 'Views', 'Downloads', 'Last Accessed', 'Product/Course',
            ], $downloads->getCollection()->map(fn ($d) => [
                $d->resource?->title,
                $d->user?->name ?? 'Guest',
                '—',
                1,
                $d->created_at->format('Y-m-d H:i'),
                $d->resource?->category?->name ?? '—',
            ]));
        }

        return view('admin.reports.resource-usage', compact('downloads'));
    }

    public function superLiveLessons(Request $request)
    {
        $records = CourseEnrollment::with(['user', 'course'])
            ->whereHas('course.lessons', fn ($q) => $q->where('lesson_type', 'live_class'))
            ->when($request->search, fn ($q, $s) => $this->enrollmentSearch($q, $s))
            ->latest('updated_at')
            ->paginate(30)
            ->withQueryString();

        if ($request->boolean('export')) {
            return $this->exportCsv('super-live-lessons', [
                'Lesson', 'Learner', 'Attendance', 'Watch Duration', 'Join Time', 'Leave Time', 'Status',
            ], $records->getCollection()->map(fn ($e) => [
                $e->course?->title,
                $e->user?->name,
                '—',
                '—',
                '—',
                '—',
                $e->status,
            ]));
        }

        return view('admin.reports.super-live-lessons', compact('records'));
    }

    public function certificates(Request $request)
    {
        $certificates = Certificate::with(['user', 'course'])
            ->when($request->search, fn ($q, $s) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$s}%"))
                ->orWhere('certificate_number', 'like', "%{$s}%"))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        if ($request->boolean('export')) {
            return $this->exportCsv('certificates', ['Certificate #', 'Learner', 'Course', 'Issued'], $certificates->map(fn ($c) => [
                $c->certificate_number,
                $c->user?->name,
                $c->course?->title,
                $c->issued_at?->format('Y-m-d'),
            ]));
        }

        return view('admin.reports.certificates', compact('certificates'));
    }
}
