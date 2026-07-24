@extends('layouts.app')

@section('title', 'Customer Pages')
@section('page-title', 'Customer Pages')
@section('breadcrumb', 'Platform Admin / Customer Pages')

@section('content')
<div class="space-y-6">
    <p class="text-sm text-slate-600 max-w-2xl">
        Manage testimonials, success stories, and wall-of-love content for
        <code class="text-xs bg-slate-100 px-1.5 py-0.5 rounded">/customers/{slug}</code> pages.
        Saving testimonials or success stories also updates the homepage sections.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($pages as $page)
            <a href="{{ route('platform.customer-pages.edit', $page['key']) }}" class="glass-card rounded-2xl p-5 hover:shadow-soft transition group">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="font-semibold text-slate-900 group-hover:text-indigo-600">{{ $page['label'] }}</h3>
                        <p class="text-sm text-slate-500 mt-1">{{ $page['description'] }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full {{ $page['is_customized'] ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                        {{ $page['is_customized'] ? 'Custom' : 'Default' }}
                    </span>
                </div>
                <div class="mt-4 flex items-center justify-between text-xs text-slate-400">
                    <span>{{ $page['item_count'] }} items</span>
                    <span>
                        @if($page['updated_at'])
                            Updated {{ $page['updated_at']->diffForHumans() }}
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
