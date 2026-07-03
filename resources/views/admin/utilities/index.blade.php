@extends('layouts.app')

@section('title', 'Utilities')
@section('page-title', 'Utilities')
@section('breadcrumb', 'Products')

@section('content')
<div class="space-y-8">
    <div>
        <h3 class="text-2xl font-bold text-slate-900">Utilities</h3>
        <p class="text-sm text-slate-500 mt-2 max-w-3xl leading-relaxed">
            Utilities lets you create multiple copies of an existing course and encrypt your unencrypted courses.
        </p>
    </div>

    <div class="space-y-4">
        @foreach($utilities as $utility)
            <a href="{{ $utility['url'] }}"
               class="group glass-card rounded-2xl p-6 flex items-center justify-between gap-6 hover:border-emerald-200 hover:shadow-md transition-all duration-200">
                <div class="flex items-start gap-4 min-w-0">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center shrink-0 group-hover:bg-emerald-100 transition-colors">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $utility['icon'] }}"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-base font-bold text-slate-900 group-hover:text-emerald-700 transition-colors">{{ $utility['title'] }}</h4>
                        <p class="text-sm text-slate-500 mt-2 leading-relaxed">{{ $utility['description'] }}</p>
                        @if(!empty($utility['note']))
                            <p class="text-xs text-slate-400 mt-2">{{ $utility['note'] }}</p>
                        @endif
                    </div>
                </div>
                <svg class="w-5 h-5 text-slate-400 shrink-0 group-hover:text-emerald-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        @endforeach
    </div>
</div>
@endsection
