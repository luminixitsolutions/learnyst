{{-- Horizontal top bar skeleton --}}
<header class="panel-sidebar-skeleton panel-sidebar-skeleton-horizontal fixed top-0 left-0 right-0 z-[55] border-b border-slate-200/80 bg-slate-50/95" aria-hidden="true">
    <div class="flex items-center gap-3 px-4 h-16 border-b border-slate-200/60">
        <div class="panel-skeleton-block w-9 h-9 rounded-xl shrink-0"></div>
        <div class="space-y-2">
            <div class="panel-skeleton-block h-3.5 w-20 rounded-md"></div>
            <div class="panel-skeleton-block h-2.5 w-24 rounded-md"></div>
        </div>
        <div class="ml-auto flex items-center gap-2">
            <div class="panel-skeleton-block h-8 w-32 rounded-lg hidden sm:block"></div>
            <div class="panel-skeleton-block w-8 h-8 rounded-full"></div>
        </div>
    </div>

    <div class="px-4 py-2.5 overflow-hidden">
        <div class="flex items-center gap-2 min-w-max">
            <div class="panel-skeleton-block h-8 w-24 rounded-xl shrink-0"></div>
            @foreach(range(1, 11) as $item)
            <div class="panel-skeleton-block h-8 rounded-xl shrink-0 {{ $item % 3 === 0 ? 'w-28' : ($item % 2 === 0 ? 'w-32' : 'w-24') }}"></div>
            @endforeach
        </div>
    </div>
</header>
