<header class="panel-topbar sticky top-0 z-30 bg-white/95 backdrop-blur-xl border-b shadow-sm" style="border-bottom-color: var(--topbar-border, rgba(13, 148, 136, 0.28));">
    @php
        use App\Services\PlatformImpersonationService;

        $topbarHorizontalNav = false;
        if (auth()->check() && request()->is('company', 'company/*')) {
            try {
                $topbarSettings = \App\Services\SidebarSettingsService::forCompany(
                    \App\Services\CompanyService::resolveForUser(auth()->user())
                );
                $topbarHorizontalNav = ($topbarSettings['layout'] ?? 'vertical') === 'horizontal';
            } catch (\Throwable $e) {
                // keep false
            }
        }
    @endphp
    @if(PlatformImpersonationService::isActive())
        <div class="flex items-center justify-between gap-3 border-b border-amber-200 bg-amber-50 px-4 py-2 text-sm text-amber-900 sm:px-6 lg:px-8">
            <span>You are viewing an institute panel as platform admin.</span>
            <form method="POST" action="{{ route('admin.exit-platform-view') }}">
                @csrf
                <button type="submit" class="font-semibold text-amber-900 underline underline-offset-2 hover:text-amber-950">
                    Return to platform admin
                </button>
            </form>
        </div>
    @endif
    <div class="flex items-center justify-between px-4 sm:px-6 lg:px-8 h-16">
        <div class="flex items-center gap-4">
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-xl text-slate-500 hover:text-teal-700 hover:bg-teal-50 border border-transparent hover:border-teal-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div>
                <h2 class="text-lg font-bold text-slate-800" style="font-family: Inter, sans-serif;">@yield('page-title', 'Dashboard')</h2>
                @hasSection('breadcrumb')
                    <div class="text-xs text-slate-500 mt-0.5">@yield('breadcrumb')</div>
                @endif
            </div>
        </div>
        @if(! $topbarHorizontalNav)
        <div class="flex items-center gap-2">
            @include('layouts.partials.user-menu-dropdown', ['variant' => 'topbar'])
        </div>
        @endif
    </div>
</header>
