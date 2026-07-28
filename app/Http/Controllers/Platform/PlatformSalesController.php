<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\GstInvoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\SubscriptionPackage;
use App\Services\PlatformSalesService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PlatformSalesController extends Controller
{
    public function __construct(protected PlatformSalesService $sales) {}

    public function orders(Request $request)
    {
        $companies = Company::orderBy('name')->get(['id', 'name', 'owner_user_id']);
        $byOwner = $this->sales->companiesByOwner();

        $query = Order::query()->with(['user', 'items.course'])->latest();
        $this->applyOrderFilters($query, $request);

        $baseStatsQuery = Order::query();
        $this->applyOrderFilters($baseStatsQuery, $request);

        $stats = [
            'orders' => (clone $baseStatsQuery)->count(),
            'paid' => (clone $baseStatsQuery)->where('payment_status', 'paid')->count(),
            'revenue' => (float) (clone $baseStatsQuery)->where('payment_status', 'paid')->sum('total'),
            'refunded' => (clone $baseStatsQuery)->where(function ($q) {
                $q->whereIn('payment_status', ['refunded', 'partial_refund'])
                    ->orWhere(function ($qq) {
                        $qq->whereNotNull('refund_status')->where('refund_status', '!=', 'none');
                    });
            })->count(),
        ];

        $orders = $query->paginate(30)->withQueryString();
        $orders->getCollection()->transform(function (Order $order) use ($byOwner) {
            $order->setAttribute('institute', $this->sales->resolveCompanyForOrder($order, $byOwner));

            return $order;
        });

        return view('platform.sales.orders', compact('orders', 'companies', 'stats'));
    }

    public function orderShow(Order $order)
    {
        $order->load(['user', 'items.course', 'payments', 'gstInvoice', 'coupon']);
        $institute = $this->sales->resolveCompanyForOrder($order);

        return view('platform.sales.order-show', compact('order', 'institute'));
    }

    public function ordersExport(Request $request): StreamedResponse
    {
        $byOwner = $this->sales->companiesByOwner();
        $query = Order::query()->with(['user', 'items.course'])->latest();
        $this->applyOrderFilters($query, $request);

        return response()->streamDownload(function () use ($query, $byOwner) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['order_number', 'date', 'customer', 'email', 'institute', 'status', 'refund_status', 'total', 'paid_at']);
            $query->chunk(200, function ($chunk) use ($out, $byOwner) {
                foreach ($chunk as $order) {
                    $institute = $this->sales->resolveCompanyForOrder($order, $byOwner);
                    fputcsv($out, [
                        $order->order_number,
                        $order->created_at?->toDateTimeString(),
                        $order->user?->name,
                        $order->user?->email,
                        $institute?->name,
                        $order->payment_status,
                        $order->refund_status,
                        $order->total,
                        $order->paid_at?->toDateTimeString(),
                    ]);
                }
            });
            fclose($out);
        }, 'platform-orders-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function payments(Request $request)
    {
        $companies = Company::orderBy('name')->get(['id', 'name']);
        $byOwner = $this->sales->companiesByOwner();

        $query = Payment::query()->with(['user', 'order.items.course', 'order.user'])->latest();
        $this->applyPaymentFilters($query, $request);

        $stats = [
            'total' => (clone $query)->count(),
            'success_amount' => (float) (clone $query)->where('status', 'success')->sum('amount'),
            'failed' => (clone $query)->where('status', 'failed')->count(),
            'refunded' => (clone $query)->where('status', 'refunded')->count(),
        ];

        $payments = $query->paginate(30)->withQueryString();
        $payments->getCollection()->transform(function (Payment $payment) use ($byOwner) {
            $payment->setAttribute('institute', $this->sales->resolveCompanyForPayment($payment, $byOwner));

            return $payment;
        });

        $gstTotals = null;
        if (class_exists(GstInvoice::class)) {
            $gstQuery = GstInvoice::query();
            if ($request->filled('company_id')) {
                $ownerId = Company::where('id', (int) $request->company_id)->value('owner_user_id');
                if ($ownerId) {
                    $gstQuery->where('created_by', $ownerId);
                }
            }
            if ($request->filled('from')) {
                $gstQuery->whereDate('invoice_date', '>=', $request->from);
            }
            if ($request->filled('to')) {
                $gstQuery->whereDate('invoice_date', '<=', $request->to);
            }
            $gstTotals = [
                'count' => (clone $gstQuery)->count(),
                'total' => (float) (clone $gstQuery)->sum('total'),
                'tax' => (float) (clone $gstQuery)->selectRaw('COALESCE(SUM(cgst_amount + sgst_amount + igst_amount),0) as tax')->value('tax'),
            ];
        }

        return view('platform.sales.payments', compact('payments', 'companies', 'stats', 'gstTotals'));
    }

    public function paymentShow(Payment $payment)
    {
        $payment->load(['user', 'order.items.course', 'order.gstInvoice']);
        $institute = $this->sales->resolveCompanyForPayment($payment);

        return view('platform.sales.payment-show', compact('payment', 'institute'));
    }

    public function paymentsExport(Request $request): StreamedResponse
    {
        $byOwner = $this->sales->companiesByOwner();
        $query = Payment::query()->with(['user', 'order.items.course', 'order.user'])->latest();
        $this->applyPaymentFilters($query, $request);

        return response()->streamDownload(function () use ($query, $byOwner) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id', 'date', 'transaction_id', 'gateway', 'amount', 'status', 'order_number', 'customer', 'institute']);
            $query->chunk(200, function ($chunk) use ($out, $byOwner) {
                foreach ($chunk as $payment) {
                    $institute = $this->sales->resolveCompanyForPayment($payment, $byOwner);
                    fputcsv($out, [
                        $payment->id,
                        $payment->created_at?->toDateTimeString(),
                        $payment->transaction_id,
                        $payment->gateway,
                        $payment->amount,
                        $payment->status,
                        $payment->order?->order_number,
                        $payment->user?->email ?? $payment->order?->user?->email,
                        $institute?->name,
                    ]);
                }
            });
            fclose($out);
        }, 'platform-payments-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function revenueByInstitute(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        $rows = Company::query()
            ->with('subscriptionPackage')
            ->orderBy('name')
            ->get()
            ->map(function (Company $company) use ($from, $to) {
                $ownerId = $company->owner_user_id;
                $base = Order::query()
                    ->where('payment_status', 'paid')
                    ->where(function ($q) use ($ownerId) {
                        $q->whereHas('items.course', fn ($c) => $c->where('created_by', $ownerId))
                            ->orWhereHas('user', fn ($u) => $u->where('created_by', $ownerId));
                    });
                if ($from) {
                    $base->whereRaw('DATE(COALESCE(paid_at, created_at)) >= ?', [$from]);
                }
                if ($to) {
                    $base->whereRaw('DATE(COALESCE(paid_at, created_at)) <= ?', [$to]);
                }

                return [
                    'company' => $company,
                    'orders' => (clone $base)->count(),
                    'revenue' => (float) (clone $base)->sum('total'),
                    'avg_order' => (float) ((clone $base)->avg('total') ?? 0),
                ];
            })
            ->sortByDesc('revenue')
            ->values();

        $totals = [
            'institutes' => $rows->count(),
            'orders' => $rows->sum('orders'),
            'revenue' => $rows->sum('revenue'),
        ];

        return view('platform.sales.revenue', compact('rows', 'totals'));
    }

    public function revenueExport(Request $request): StreamedResponse
    {
        // Reuse same computation via internal request to revenueByInstitute data
        $request->merge($request->query());
        $from = $request->query('from');
        $to = $request->query('to');

        $rows = Company::query()->orderBy('name')->get()->map(function (Company $company) use ($from, $to) {
            $ownerId = $company->owner_user_id;
            $base = Order::query()
                ->where('payment_status', 'paid')
                ->where(function ($q) use ($ownerId) {
                    $q->whereHas('items.course', fn ($c) => $c->where('created_by', $ownerId))
                        ->orWhereHas('user', fn ($u) => $u->where('created_by', $ownerId));
                });
            if ($from) {
                $base->whereRaw('DATE(COALESCE(paid_at, created_at)) >= ?', [$from]);
            }
            if ($to) {
                $base->whereRaw('DATE(COALESCE(paid_at, created_at)) <= ?', [$to]);
            }

            return [
                $company->name,
                $company->is_active ? 'active' : 'suspended',
                (clone $base)->count(),
                (float) (clone $base)->sum('total'),
            ];
        });

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['institute', 'status', 'paid_orders', 'revenue']);
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, 'platform-revenue-by-institute-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function packagesOverview(Request $request)
    {
        $packages = SubscriptionPackage::query()->ordered()->get();

        $query = Company::query()->with(['subscriptionPackage', 'owner'])->orderBy('name');
        if ($request->filled('package_id')) {
            if ($request->package_id === 'none') {
                $query->whereNull('subscription_package_id');
            } else {
                $query->where('subscription_package_id', (int) $request->package_id);
            }
        }
        if ($request->filled('status')) {
            match ($request->status) {
                'active' => $query->where('is_active', true),
                'suspended' => $query->where('is_active', false),
                default => null,
            };
        }

        $companies = $query->paginate(40)->withQueryString();

        $packageStats = $packages->map(function (SubscriptionPackage $package) {
            return [
                'package' => $package,
                'institutes' => Company::where('subscription_package_id', $package->id)->count(),
                'active' => Company::where('subscription_package_id', $package->id)->where('is_active', true)->count(),
            ];
        });

        $unassigned = Company::whereNull('subscription_package_id')->count();

        return view('platform.sales.packages', compact('companies', 'packages', 'packageStats', 'unassigned'));
    }

    public function packagesExport(Request $request): StreamedResponse
    {
        $query = Company::query()->with(['subscriptionPackage', 'owner'])->orderBy('name');
        if ($request->filled('package_id')) {
            if ($request->package_id === 'none') {
                $query->whereNull('subscription_package_id');
            } else {
                $query->where('subscription_package_id', (int) $request->package_id);
            }
        }

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['institute', 'owner_email', 'package', 'institute_status', 'public', 'package_assigned_at']);
            $query->chunk(100, function ($chunk) use ($out) {
                foreach ($chunk as $company) {
                    fputcsv($out, [
                        $company->name,
                        $company->owner?->email,
                        $company->subscriptionPackage?->name ?? 'None',
                        $company->is_active ? 'active' : 'suspended',
                        $company->is_public ? 'public' : 'hidden',
                        $company->package_assigned_at?->toDateTimeString(),
                    ]);
                }
            });
            fclose($out);
        }, 'platform-packages-overview-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    protected function applyOrderFilters($query, Request $request): void
    {
        if ($request->filled('company_id')) {
            $this->sales->scopeOrdersForCompany($query, (int) $request->company_id);
        }
        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('gateway_order_id', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            });
        }
    }

    protected function applyPaymentFilters($query, Request $request): void
    {
        if ($request->filled('company_id')) {
            $this->sales->scopePaymentsForCompany($query, (int) $request->company_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                    ->orWhereHas('order', fn ($o) => $o->where('order_number', 'like', "%{$search}%"))
                    ->orWhereHas('user', fn ($u) => $u->where('email', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%"));
            });
        }
    }
}
