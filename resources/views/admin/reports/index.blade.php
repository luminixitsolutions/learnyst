@extends('layouts.app')

@section('title', 'Reports')
@section('page-title', 'Reports')
@section('breadcrumb', 'Analytics & Reports')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach([
        ['Sales Report', 'Revenue and order analytics', 'admin.reports.sales'],
        ['Learners Report', 'Top learners by spending', 'admin.reports.learners'],
        ['Courses Report', 'Enrollment and progress', 'admin.reports.courses'],
        ['Enrollment Report', 'All learner enrollments', 'admin.reports.enrollments'],
        ['Bundle Report', 'Bundle sales and enrollments', 'admin.reports.bundles'],
        ['Payments Report', 'Payment transactions', 'admin.reports.payments'],
        ['Batches Report', 'Batch enrollment stats', 'admin.reports.batches'],
        ['Certificates Report', 'Issued certificates', 'admin.reports.certificates'],
    ] as [$title, $desc, $route])
    <a href="{{ route($route) }}" class="glass-card rounded-2xl p-6 hover:border-indigo-400/30 transition group">
        <h3 class="text-lg font-bold text-slate-800 group-hover:text-indigo-600">{{ $title }}</h3>
        <p class="text-sm text-slate-500 mt-2">{{ $desc }}</p>
        <span class="inline-block mt-4 text-sm text-indigo-600">View report →</span>
    </a>
    @endforeach
</div>
@endsection
