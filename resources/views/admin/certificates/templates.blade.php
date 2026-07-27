@extends('layouts.app')

@section('title', 'Certificate Templates')
@section('page-title', 'Certificate Templates')
@section('breadcrumb', 'Certificates / Templates')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <a href="{{ route('admin.certificates.index') }}" class="text-sm text-slate-500 hover:text-white">← Back to certificates</a>
    </div>

    <div class="glass-card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Create Template</h3>
        <form method="POST" action="{{ route('admin.certificates.templates.store') }}" class="space-y-4">
            @csrf
            <x-form-input label="Template Name" name="name" required />
            <x-form-input label="HTML Content" name="html_content" type="textarea" required placeholder="Use @{{student_name}}, @{{course_name}}, @{{issue_date}} placeholders" />
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-form-input label="Validity (years)" name="validity_years" type="number" min="0" placeholder="e.g. 2" />
                <x-form-input label="Validity (days)" name="validity_days" type="number" min="0" placeholder="Optional extra days" />
                <x-form-input label="Renewal price (INR)" name="renewal_price" type="number" step="0.01" min="0" placeholder="0 = free" />
            </div>
            <label class="flex items-center gap-3">
                <input type="hidden" name="requires_renewal_assessment" value="0">
                <input type="checkbox" name="requires_renewal_assessment" value="1" class="rounded border-slate-600 bg-slate-800 text-brand-500">
                <span class="text-sm text-slate-300">Require refresher assessment on renewal</span>
            </label>
            <label class="flex items-center gap-3">
                <input type="hidden" name="is_default" value="0">
                <input type="checkbox" name="is_default" value="1" class="rounded border-slate-600 bg-slate-800 text-brand-500">
                <span class="text-sm text-slate-300">Set as default template</span>
            </label>
            <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary">Create Template</button>
        </form>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden">
        @if($templates->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm panel-table">
                <thead><tr class="text-left">
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Validity</th>
                        <th class="px-6 py-4">Renewal</th>
                        <th class="px-6 py-4">Default</th>
                        <th class="px-6 py-4">Created</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($templates as $template)
                    <tr>
                        <td class="px-6 py-4 text-white font-medium">{{ $template->name }}</td>
                        <td class="px-6 py-4 text-slate-500 text-xs">
                            @if($template->hasValidityPeriod())
                                {{ $template->validity_years ? $template->validity_years.'y' : '' }}
                                {{ $template->validity_days ? $template->validity_days.'d' : '' }}
                            @else
                                No expiry
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-500">₹{{ number_format($template->renewal_price ?? 0, 2) }}</td>
                        <td class="px-6 py-4">@if($template->is_default)<x-badge type="success">Default</x-badge>@else—@endif</td>
                        <td class="px-6 py-4 text-slate-500">{{ $template->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <form method="POST" action="{{ route('admin.certificates.templates.renewal', $template) }}" class="flex flex-wrap gap-2 items-end">
                                @csrf
                                @method('PUT')
                                <input type="number" name="validity_years" value="{{ $template->validity_years }}" placeholder="Years" class="panel-input w-20 text-xs py-1">
                                <input type="number" name="validity_days" value="{{ $template->validity_days }}" placeholder="Days" class="panel-input w-20 text-xs py-1">
                                <input type="number" step="0.01" name="renewal_price" value="{{ $template->renewal_price }}" placeholder="Price" class="panel-input w-24 text-xs py-1">
                                <button type="submit" class="text-xs text-indigo-600 font-semibold">Save</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-200">{{ $templates->links() }}</div>
        @else
        <x-empty-state title="No templates yet" description="Create your first certificate template above." />
        @endif
    </div>
</div>
@endsection
