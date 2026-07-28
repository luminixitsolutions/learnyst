@extends('layouts.app')

@section('title', 'AI Draft')
@section('page-title', $generation->title)
@section('breadcrumb', 'AI Center / Draft')

@section('content')
<div class="space-y-6 max-w-4xl">
    <a href="{{ route('admin.ai.index') }}" class="text-sm text-emerald-400">← Back</a>
    <div class="glass-card rounded-2xl p-6">
        <div class="flex justify-between gap-3 mb-4">
            <div>
                <h3 class="text-xl font-bold text-white">{{ $generation->title }}</h3>
                <p class="text-xs text-slate-500 mt-1">{{ $generation->feature }} · {{ $generation->status }}</p>
            </div>
            <form method="POST" action="{{ route('admin.ai.status', $generation) }}" class="flex gap-2 items-center">
                @csrf
                <select name="status" class="rounded-lg bg-slate-800 border-slate-600 text-sm text-white">
                    @foreach(['draft','approved','published','rejected'] as $st)
                        <option value="{{ $st }}" @selected($generation->status===$st)>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
                <button class="px-3 py-2 rounded-lg panel-btn-primary text-sm">Update</button>
            </form>
        </div>
        <div class="mb-4">
            <p class="text-xs text-slate-500 mb-1">Prompt</p>
            <pre class="text-sm text-slate-300 whitespace-pre-wrap bg-slate-900/50 rounded-xl p-4">{{ $generation->prompt }}</pre>
        </div>
        <div>
            <p class="text-xs text-slate-500 mb-1">Output (copy into course / quiz / assignment after approve)</p>
            <pre class="text-sm text-white whitespace-pre-wrap bg-slate-900/80 rounded-xl p-4 border border-slate-700">{{ $generation->output }}</pre>
        </div>
    </div>
</div>
@endsection
