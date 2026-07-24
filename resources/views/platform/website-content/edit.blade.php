@extends('layouts.app')

@section('title', $meta['label'])
@section('page-title', $meta['label'])
@section('breadcrumb', 'Platform Admin / Website Content / ' . $meta['label'])

@section('content')
<div class="space-y-6 max-w-5xl">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <a href="{{ route('platform.website-content.index') }}" class="text-sm text-indigo-600 hover:underline">← All sections</a>
            <p class="text-sm text-slate-500 mt-1">{{ $meta['description'] }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('home') }}" target="_blank" class="panel-btn-secondary">Preview</a>
            <form method="POST" action="{{ route('platform.website-content.reset', $section) }}" onsubmit="return confirm('Reset this section to defaults?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="panel-btn-secondary text-red-600">Reset</button>
            </form>
        </div>
    </div>

    <form method="POST" action="{{ route('platform.website-content.update', $section) }}" enctype="multipart/form-data" class="glass-card rounded-2xl p-6 space-y-6">
        @csrf
        @method('PUT')

        @includeIf('platform.website-content.partials.' . $section)

        <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
            <button type="submit" class="panel-btn-primary">Save changes</button>
            <a href="{{ route('platform.website-content.index') }}" class="text-sm text-slate-500 hover:text-slate-700">Cancel</a>
        </div>
    </form>
</div>
@endsection
