@extends('layouts.app')

@section('title', 'Reports')
@section('page-title', 'Reports')
@section('breadcrumb', 'Analytics & Reports')

@section('content')
<div class="space-y-8">
    @foreach([
        'Enrollment & Access' => [
            ['Learner Enrollments', 'All enrollment records with access dates', 'admin.reports.enrollments'],
            ['Batches Report', 'Batch schedules and learner counts', 'admin.reports.batches'],
            ['Bundle Report', 'Bundle catalog and enrollments', 'admin.reports.bundles'],
            ['Certificates Report', 'Issued certificates', 'admin.reports.certificates'],
        ],
        'Financial' => [
            ['Sales Reports', 'Product sales, coupons, affiliates & broadcast', 'admin.reports.sales.index'],
            ['Transactions', 'Orders, payments and transaction IDs', 'admin.reports.transactions'],
            ['Payment Gateways', 'Gateway configuration and usage', 'admin.reports.payment-gateways'],
            ['School Payouts', 'Payout history by gateway', 'admin.reports.school-payouts'],
        ],
        'Progress & Scores' => [
            ['Product Progress', 'Ebook, podcast, quiz, mock test & more', 'admin.reports.progress.index'],
            ['Bundle Progress', 'Per-learner bundle completion', 'admin.reports.bundle-progress'],
            ['Custom Product Progress', 'Custom product learner progress', 'admin.reports.custom-product-progress'],
            ['Test Series Scores', 'Scores and pass/fail status', 'admin.reports.test-series-scores'],
            ['Course Report', 'Enrollment and average progress', 'admin.reports.courses'],
        ],
        'Learners' => [
            ['Learners Report', 'Sales, visits, billing and signup data', 'admin.reports.learners'],
        ],
        'Engagement' => [
            ['Resource Usage', 'Downloads and resource access', 'admin.reports.resource-usage'],
            ['Live Class Attendance', 'Join/leave and attendance status', 'admin.reports.live-class-attendance'],
            ['Zoom Insights', 'Zoom session analytics', 'admin.reports.zoom-insights'],
            ['Super Live Lessons', 'Live lesson watch duration', 'admin.reports.super-live-lessons'],
        ],
    ] as $group => $cards)
    <div>
        <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-3">{{ $group }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($cards as [$title, $desc, $route])
            <a href="{{ route($route) }}" class="glass-card rounded-2xl p-6 hover:border-indigo-400/30 transition group">
                <h3 class="text-lg font-bold text-slate-800 group-hover:text-indigo-600">{{ $title }}</h3>
                <p class="text-sm text-slate-500 mt-2">{{ $desc }}</p>
                <span class="inline-block mt-4 text-sm text-indigo-600">View report →</span>
            </a>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endsection
