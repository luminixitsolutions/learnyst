@extends('layouts.app')

@section('title', 'Learners Report')
@section('page-title', 'Learners Report')
@section('breadcrumb', 'Reports / Learners')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <p class="text-sm text-slate-500">Top learners by total spent</p>
        <a href="{{ route('admin.reports.index') }}" class="text-sm text-slate-500 hover:text-white">← All Reports</a>
    </div>
    <div class="glass-card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                        <th class="px-6 py-4">Learner</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Enrollments</th>
                        <th class="px-6 py-4">Total Spent</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($learners as $learner)
                    <tr>
                        <td class="px-6 py-4"><a href="{{ route('admin.learners.show', $learner) }}" class="text-white hover:text-indigo-600">{{ $learner->name }}</a></td>
                        <td class="px-6 py-4 text-slate-500">{{ $learner->email }}</td>
                        <td class="px-6 py-4 text-white">{{ $learner->enrollments_count }}</td>
                        <td class="px-6 py-4 text-indigo-600 font-medium">₹{{ number_format($learner->total_spent ?? 0, 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $learners->links() }}</div>
    </div>
</div>
@endsection
