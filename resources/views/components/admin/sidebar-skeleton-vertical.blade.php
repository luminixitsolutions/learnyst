{{-- Vertical sidebar skeleton --}}
<aside class="panel-sidebar-skeleton panel-sidebar-skeleton-vertical fixed inset-y-0 left-0 z-[55] w-72 overflow-hidden border-r border-slate-200/80 bg-slate-50/95" aria-hidden="true">
    <div class="flex flex-col h-full p-5 space-y-5">
        <div class="flex items-center gap-3 pb-4 border-b border-slate-200/70">
            <div class="panel-skeleton-block w-10 h-10 rounded-xl shrink-0"></div>
            <div class="flex-1 space-y-2">
                <div class="panel-skeleton-block h-4 w-24 rounded-lg"></div>
                <div class="panel-skeleton-block h-3 w-20 rounded-md"></div>
            </div>
        </div>

        <div class="flex-1 space-y-2 py-1">
            @foreach(range(1, 10) as $item)
            <div class="flex items-center gap-3 px-2 py-2.5 rounded-xl">
                <div class="panel-skeleton-block w-8 h-8 rounded-lg shrink-0"></div>
                <div class="panel-skeleton-block h-3.5 flex-1 max-w-[140px] rounded-md"></div>
            </div>
            @endforeach
        </div>

        <div class="pt-4 border-t border-slate-200/70">
            <div class="flex items-center gap-3 px-2 py-2 rounded-xl">
                <div class="panel-skeleton-block w-9 h-9 rounded-full shrink-0"></div>
                <div class="flex-1 space-y-2">
                    <div class="panel-skeleton-block h-3.5 w-28 rounded-md"></div>
                    <div class="panel-skeleton-block h-3 w-36 rounded-md"></div>
                </div>
            </div>
        </div>
    </div>
</aside>
