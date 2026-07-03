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
                        <th class="px-6 py-4">Default</th>
                        <th class="px-6 py-4">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($templates as $template)
                    <tr>
                        <td class="px-6 py-4 text-white font-medium">{{ $template->name }}</td>
                        <td class="px-6 py-4">@if($template->is_default)<x-badge type="success">Default</x-badge>@else—@endif</td>
                        <td class="px-6 py-4 text-slate-500">{{ $template->created_at->format('M d, Y') }}</td>
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
