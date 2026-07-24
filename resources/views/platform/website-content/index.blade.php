@extends('layouts.app')

@section('title', 'Website Content')
@section('page-title', 'Website Content')
@section('breadcrumb', 'Platform Admin / Website Content')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <p class="text-sm text-slate-600 max-w-2xl">
                Manage marketing website content — hero slider, homepage sections, testimonials, and brand details.
                Changes appear on the public site immediately.
            </p>
        </div>
        <a href="{{ route('home') }}" target="_blank" class="panel-btn-secondary inline-flex items-center gap-2">
            Preview website
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($sections as $section)
            <a href="{{ route('platform.website-content.edit', $section['key']) }}" class="glass-card rounded-2xl p-5 hover:shadow-soft transition group">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="font-semibold text-slate-900 group-hover:text-indigo-600">{{ $section['label'] }}</h3>
                        <p class="text-sm text-slate-500 mt-1">{{ $section['description'] }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full {{ $section['is_customized'] ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                        {{ $section['is_customized'] ? 'Custom' : 'Default' }}
                    </span>
                </div>
                <div class="mt-4 flex items-center justify-between text-xs text-slate-400">
                    <span>{{ ucfirst($section['group']) }}</span>
                    <span>
                        @if($section['updated_at'])
                            Updated {{ $section['updated_at']->diffForHumans() }}
                        @else
                            Using defaults
                        @endif
                    </span>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection
