@props(['title', 'createRoute' => null, 'createLabel' => 'Create', 'exportRoute' => null, 'importRoute' => null])

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        @if($title)
            <h2 class="text-xl font-bold text-slate-800">{{ $title }}</h2>
        @endif
        {{ $slot }}
    </div>
    <div class="flex flex-wrap items-center gap-2">
        @if($exportRoute)
            <a href="{{ $exportRoute }}" class="panel-btn-secondary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export
            </a>
        @endif
        @if($createRoute)
            <a href="{{ $createRoute }}" class="panel-btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ $createLabel }}
            </a>
        @endif
    </div>
</div>
