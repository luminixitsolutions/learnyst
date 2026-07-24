@extends('layouts.app')

@section('title', 'Signup Form')
@section('page-title', 'Signup Form Options')
@section('breadcrumb', 'Platform Admin / Signup Form')

@section('content')
<div class="space-y-6">
    <p class="text-sm text-slate-600 max-w-2xl">
        Manage the multiple-choice options shown during educator signup (business type, what they teach, goals, and more).
    </p>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($questions as $question)
            <a href="{{ route('platform.signup-form.edit', $question['key']) }}" class="glass-card rounded-2xl p-5 hover:shadow-soft transition group">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="font-semibold text-slate-900 group-hover:text-indigo-600">{{ $question['label'] }}</h3>
                        <p class="text-sm text-slate-500 mt-1">{{ $question['description'] }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full {{ $question['is_customized'] ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                        {{ $question['is_customized'] ? 'Custom' : 'Default' }}
                    </span>
                </div>
                <div class="mt-4 flex items-center justify-between text-xs text-slate-400">
                    <span>{{ $question['option_count'] }} active options</span>
                    <span>
                        @if($question['updated_at'])
                            Updated {{ $question['updated_at']->diffForHumans() }}
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
