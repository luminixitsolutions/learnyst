@extends('layouts.app')

@section('title', 'Solution Pages')
@section('page-title', 'Solution Pages')
@section('breadcrumb', 'Platform Admin / Solution Pages')

@section('content')
<div class="space-y-8">
    <p class="text-sm text-slate-600 max-w-2xl">
        Edit the marketing content for each <code class="text-xs bg-slate-100 px-1.5 py-0.5 rounded">/solutions/{slug}</code> page — hero, features, benefits, FAQs, and CTAs.
    </p>

    @foreach($solutions as $groupLabel => $items)
        <div class="space-y-4">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">{{ $groupLabel }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($items as $solution)
                    <a href="{{ route('platform.solution-pages.edit', $solution['key']) }}" class="glass-card rounded-2xl p-5 hover:shadow-soft transition group">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-semibold text-slate-900 group-hover:text-indigo-600">{{ $solution['label'] }}</h3>
                                <p class="text-sm text-slate-500 mt-1">{{ $solution['description'] }}</p>
                            </div>
                            <span class="text-xs px-2 py-1 rounded-full {{ $solution['is_customized'] ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $solution['is_customized'] ? 'Custom' : 'Default' }}
                            </span>
                        </div>
                        <div class="mt-4 flex items-center justify-between text-xs text-slate-400">
                            <span>{{ $solution['feature_count'] }} features</span>
                            <span>
                                @if($solution['updated_at'])
                                    Updated {{ $solution['updated_at']->diffForHumans() }}
                                @else
                                    Using defaults
                                @endif
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
@endsection
