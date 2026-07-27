{{-- Generic admin page skeleton — toolbar + datatable card --}}
<div class="panel-skeleton space-y-6" aria-hidden="true">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="panel-skeleton-block h-4 w-56 max-w-full rounded-lg"></div>
        <div class="flex gap-2">
            <div class="panel-skeleton-block h-10 w-28 rounded-xl"></div>
            <div class="panel-skeleton-block h-10 w-36 rounded-xl"></div>
        </div>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden border border-slate-200/80">
        <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-b border-slate-100 bg-slate-50/80">
            <div class="panel-skeleton-block h-9 w-32 rounded-full"></div>
            <div class="panel-skeleton-block h-9 w-52 rounded-full"></div>
        </div>

        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/50">
            <div class="grid grid-cols-5 gap-4">
                @foreach(range(1, 5) as $col)
                    <div class="panel-skeleton-block h-3 rounded {{ $col === 5 ? 'w-16 ml-auto' : 'w-full' }}"></div>
                @endforeach
            </div>
        </div>

        @foreach(range(1, 6) as $row)
            <div class="px-5 py-4 border-b border-slate-50 last:border-0">
                <div class="grid grid-cols-5 gap-4 items-center">
                    <div class="col-span-1 space-y-2">
                        <div class="panel-skeleton-block h-4 w-full max-w-[180px] rounded"></div>
                        <div class="panel-skeleton-block h-3 w-20 rounded"></div>
                    </div>
                    <div class="panel-skeleton-block h-4 w-16 rounded"></div>
                    <div class="panel-skeleton-block h-4 w-24 rounded"></div>
                    <div class="panel-skeleton-block h-4 w-20 rounded"></div>
                    <div class="flex justify-end gap-2">
                        <div class="panel-skeleton-block h-8 w-8 rounded-lg"></div>
                        <div class="panel-skeleton-block h-8 w-8 rounded-lg"></div>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-t border-slate-100 bg-slate-50/50">
            <div class="panel-skeleton-block h-4 w-44 rounded"></div>
            <div class="flex gap-2">
                @foreach(range(1, 3) as $i)
                    <div class="panel-skeleton-block h-9 w-9 rounded-full"></div>
                @endforeach
            </div>
        </div>
    </div>
</div>
