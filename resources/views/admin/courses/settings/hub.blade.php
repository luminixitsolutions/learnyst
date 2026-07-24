@extends('layouts.app')

@section('title', 'Course Settings')
@section('page-title', 'Course Settings')
@section('breadcrumb', $course->title)

@section('content')
<div class="max-w-6xl mx-auto space-y-8" x-data="{ q: '' }">
    <div class="flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('admin.courses.builder', $course) }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-800 mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back
            </a>
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Course Settings</h1>
            <p class="text-slate-500 mt-1">Manage course settings and preferences for <span class="font-medium text-slate-700">{{ $course->title }}</span></p>
        </div>
    </div>

    <div class="relative">
        <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
        <input type="search" x-model="q" placeholder="Search settings (alt+k)"
               class="w-full pl-12 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
    </div>

    @foreach($groups as $groupKey => $group)
        <section class="space-y-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900">{{ $group['label'] }}</h2>
                <p class="text-sm text-slate-500">{{ $group['description'] }}</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($group['panels'] as $panelKey)
                    @php $p = $panels[$panelKey]; @endphp
                    <a href="{{ route('admin.courses.settings.show', [$course, $panelKey]) }}"
                       x-show="!q || '{{ strtolower($p['title'].' '.$p['description']) }}'.includes(q.toLowerCase())"
                       class="group bg-white border border-slate-200 rounded-2xl p-5 hover:border-emerald-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:bg-emerald-100 transition">
                                @include('admin.courses.settings.partials.icon', ['icon' => $p['icon']])
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <h3 class="font-semibold text-slate-900">{{ $p['title'] }}</h3>
                                    <span class="text-[11px] font-medium px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">{{ $statuses[$p['status_key']] ?? '' }}</span>
                                </div>
                                <p class="text-sm text-slate-500 mt-1 line-clamp-2">{{ $p['description'] }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endforeach
</div>
@endsection
