@extends('layouts.app')

@section('title', 'Live Classes')
@section('page-title', 'Live Classes')
@section('breadcrumb', 'Course Management / Live Classes')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <p class="text-sm text-slate-500">Schedule and manage live classroom sessions.</p>
        <a href="{{ route('admin.live-classes.create') }}" class="panel-btn-primary">Schedule Class</a>
    </div>
    <div class="glass-card rounded-2xl overflow-hidden">
        @if($classes->count())
        <table class="w-full text-sm panel-table">
            <thead><tr class="text-left">
                <th class="px-6 py-4">Title</th><th class="px-6 py-4">Course</th><th class="px-6 py-4">Instructor</th>
                <th class="px-6 py-4">Date</th><th class="px-6 py-4">Platform</th><th class="px-6 py-4">Status</th><th class="px-6 py-4"></th>
            </tr></thead>
            <tbody>
                @foreach($classes as $class)
                <tr>
                    <td class="px-6 py-4 font-medium">{{ $class->title }}</td>
                    <td class="px-6 py-4">{{ $class->course?->title ?? '—' }}</td>
                    <td class="px-6 py-4">{{ $class->instructor?->name ?? '—' }}</td>
                    <td class="px-6 py-4">{{ $class->starts_at?->format('M d, Y H:i') }}</td>
                    <td class="px-6 py-4">{{ str_replace('_', ' ', ucfirst($class->platform ?? 'zoom')) }}</td>
                    <td class="px-6 py-4"><x-badge type="info">{{ ucfirst($class->status ?? 'scheduled') }}</x-badge></td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.live-classes.edit', $class) }}" class="text-indigo-600 text-sm mr-3">Edit</a>
                        <form method="POST" action="{{ route('admin.live-classes.destroy', $class) }}" class="inline">@csrf @method('DELETE')
                            <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-red-500 text-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 border-t">{{ $classes->links() }}</div>
        @else
        <x-empty-state title="No live classes scheduled" description="Create your first live class session." />
        @endif
    </div>
</div>
@endsection
