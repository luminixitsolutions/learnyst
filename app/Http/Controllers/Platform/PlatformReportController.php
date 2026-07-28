<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Services\PlatformReportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PlatformReportController extends Controller
{
    public function __construct(protected PlatformReportService $reports) {}

    public function performance(Request $request)
    {
        [$from, $to] = $this->reports->resolveDateRange(
            $request->query('from'),
            $request->query('to'),
            90
        );

        $rows = $this->reports->institutePerformance($from, $to);

        $stats = [
            'institutes' => $rows->count(),
            'revenue' => $rows->sum('revenue'),
            'learners' => $rows->sum('learners'),
            'courses' => $rows->sum('courses'),
        ];

        $chartData = $rows->take(12)->map(fn ($row) => (object) [
            'label' => $row['company']->name,
            'value' => $row['revenue'],
        ]);

        return view('platform.reports.performance', compact('rows', 'stats', 'from', 'to', 'chartData'));
    }

    public function performanceExport(Request $request): StreamedResponse
    {
        [$from, $to] = $this->reports->resolveDateRange(
            $request->query('from'),
            $request->query('to'),
            90
        );
        $rows = $this->reports->institutePerformance($from, $to);

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['rank', 'institute', 'status', 'package', 'revenue', 'paid_orders', 'learners', 'courses', 'enrollments']);
            foreach ($rows->values() as $i => $row) {
                fputcsv($out, [
                    $i + 1,
                    $row['company']->name,
                    $row['company']->is_active ? 'active' : 'suspended',
                    $row['company']->subscriptionPackage?->name ?? 'None',
                    $row['revenue'],
                    $row['orders'],
                    $row['learners'],
                    $row['courses'],
                    $row['enrollments'],
                ]);
            }
            fclose($out);
        }, 'platform-performance-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function growth(Request $request)
    {
        [$from, $to] = $this->reports->resolveDateRange(
            $request->query('from'),
            $request->query('to'),
            30
        );

        $series = $this->reports->growthSeries($from, $to);

        $stats = [
            'institutes' => $series['institutes']->sum(),
            'users' => $series['users']->sum(),
            'learners' => $series['learners']->sum(),
            'orders' => $series['orders']->sum(),
            'revenue' => $series['revenue']->sum(),
        ];

        $tableRows = $series['institutes']->keys()->map(function (string $day) use ($series) {
            return [
                'day' => $day,
                'institutes' => $series['institutes'][$day] ?? 0,
                'users' => $series['users'][$day] ?? 0,
                'learners' => $series['learners'][$day] ?? 0,
                'orders' => $series['orders'][$day] ?? 0,
                'revenue' => $series['revenue'][$day] ?? 0,
            ];
        })->reverse()->values();

        return view('platform.reports.growth', compact('series', 'stats', 'from', 'to', 'tableRows'));
    }

    public function growthExport(Request $request): StreamedResponse
    {
        [$from, $to] = $this->reports->resolveDateRange(
            $request->query('from'),
            $request->query('to'),
            30
        );
        $series = $this->reports->growthSeries($from, $to);

        return response()->streamDownload(function () use ($series) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['date', 'new_institutes', 'new_users', 'new_learners', 'orders', 'revenue']);
            foreach ($series['institutes']->keys() as $day) {
                fputcsv($out, [
                    $day,
                    $series['institutes'][$day] ?? 0,
                    $series['users'][$day] ?? 0,
                    $series['learners'][$day] ?? 0,
                    $series['orders'][$day] ?? 0,
                    $series['revenue'][$day] ?? 0,
                ]);
            }
            fclose($out);
        }, 'platform-growth-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function signupFunnel(Request $request)
    {
        [$from, $to] = $this->reports->resolveDateRange(
            $request->query('from'),
            $request->query('to'),
            90
        );

        $funnel = $this->reports->signupFunnel($from, $to);
        $reports = $this->reports;

        return view('platform.reports.signup-funnel', [
            'from' => $from,
            'to' => $to,
            'stats' => $funnel['stats'],
            'steps' => $funnel['steps'],
            'breakdowns' => $funnel['breakdowns'],
            'rows' => $funnel['rows'],
            'questions' => $funnel['questions'],
            'reports' => $reports,
        ]);
    }

    public function signupFunnelExport(Request $request): StreamedResponse
    {
        [$from, $to] = $this->reports->resolveDateRange(
            $request->query('from'),
            $request->query('to'),
            90
        );
        $funnel = $this->reports->signupFunnel($from, $to);
        $keys = array_keys($funnel['questions']);

        return response()->streamDownload(function () use ($funnel, $keys) {
            $out = fopen('php://output', 'w');
            fputcsv($out, array_merge(
                ['owner', 'email', 'institute', 'has_onboarding', 'signed_up_at'],
                $keys
            ));
            foreach ($funnel['rows'] as $row) {
                $line = [
                    $row['user']->name,
                    $row['user']->email,
                    $row['company']?->name,
                    $row['has_onboarding'] ? 'yes' : 'no',
                    $row['user']->created_at?->toDateTimeString(),
                ];
                foreach ($keys as $key) {
                    $line[] = $row['onboarding'][$key] ?? '';
                }
                fputcsv($out, $line);
            }
            fclose($out);
        }, 'platform-signup-funnel-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }
}
