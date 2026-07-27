@php
    use App\Services\PermissionService;
    use App\Services\CompanyService;
    use App\Services\SidebarSettingsService;

    $role = auth()->user()->role?->slug ?? 'admin';
    $user = auth()->user();
    $isPlatformPanel = request()->is('admin', 'admin/*');
    $isCompanyPanel = request()->is('company', 'company/*');
    $isStudentPanel = request()->is('learner', 'learner/*');

    $instructorMenu = [
        ['label' => 'Dashboard', 'route' => 'instructor.dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
    ];

    $learnerMenu = [
        ['label' => 'Dashboard', 'route' => 'learner.dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ['label' => 'My Courses', 'route' => 'learner.courses.index', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
        ['label' => 'Certificates', 'route' => 'learner.certificates', 'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
        ['label' => 'Communities', 'route' => 'learner.communities.index', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
        ['label' => 'My Profile', 'route' => 'profile.edit', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
    ];

    $platformSections = collect(config('platform-menu.platform', []));

    $companySections = collect(config('admin-menu.admin', []))->map(function ($section) use ($user) {
        if (isset($section['route'])) {
            if (!PermissionService::canAccessRoute($user, $section['permission'] ?? null)) {
                return null;
            }
            return $section;
        }
        $items = collect($section['items'] ?? [])->filter(function ($item) use ($user) {
            if (! empty($item['hidden'])) {
                return false;
            }

            return PermissionService::canAccessRoute($user, $item['permission'] ?? null);
        })->values()->all();
        if (empty($items)) {
            return null;
        }
        $section['items'] = $items;
        return $section;
    })->filter()->values()->all();

    // Show unread institute enquiry count on Website → Enquiries.
    if ($isCompanyPanel && $user && method_exists($user, 'isCompanyStaff') && $user->isCompanyStaff()) {
        try {
            $enquiryCompany = \App\Services\CompanyService::resolveForUser($user);
            $newEnquiryCount = $enquiryCompany->enquiries()->where('status', 'new')->count();
            if ($newEnquiryCount > 0) {
                $companySections = collect($companySections)->map(function ($section) use ($newEnquiryCount) {
                    if (! isset($section['items'])) {
                        return $section;
                    }
                    $section['items'] = collect($section['items'])->map(function ($item) use ($newEnquiryCount) {
                        if (($item['route'] ?? null) === 'admin.company-page.enquiries') {
                            $item['badge'] = (string) $newEnquiryCount;
                        }

                        return $item;
                    })->all();

                    return $section;
                })->all();
            }
        } catch (\Throwable $e) {
            // Ignore — menu should still render if company resolve fails.
        }
    }

    $menuSections = $isPlatformPanel ? $platformSections : $companySections;

    $sidebarSettings = SidebarSettingsService::defaults();
    if ($isCompanyPanel && $user) {
        try {
            $sidebarSettings = SidebarSettingsService::forCompany(CompanyService::resolveForUser($user));
        } catch (\Throwable $e) {
            // keep defaults
        }
        $menuSections = SidebarSettingsService::reorderSections(
            $menuSections instanceof \Illuminate\Support\Collection ? $menuSections->all() : (array) $menuSections,
            $sidebarSettings['menu_order']
        );
    }
    $menuSections = collect($menuSections);
    $sidebarLayout = $isCompanyPanel ? ($sidebarSettings['layout'] ?? 'vertical') : 'vertical';
    $panelTitle = $isPlatformPanel ? 'Platform Admin' : ($isStudentPanel || $role === 'learner' ? 'Student Panel' : 'Institute Panel');

    $isMenuItemActive = function (array $item) use ($menuSections): bool {
        $route = $item['route'] ?? null;
        if (! $route) {
            return false;
        }

        $activeRoutes = $item['active_routes'] ?? null;
        if (is_array($activeRoutes)) {
            return collect($activeRoutes)->contains(
                fn (string $pattern) => request()->routeIs($pattern)
            );
        }

        if (! request()->routeIs($route, $route . '.*')) {
            return false;
        }

        $params = $item['params'] ?? [];
        if (! empty($params)) {
            foreach ($params as $key => $value) {
                if ((string) request($key) !== (string) $value) {
                    return false;
                }
            }

            return true;
        }

        // Shared route without params: inactive when another sibling's query params match.
        foreach ($menuSections as $section) {
            $siblings = isset($section['items']) ? $section['items'] : [$section];
            foreach ($siblings as $sibling) {
                if (($sibling['route'] ?? null) !== $route) {
                    continue;
                }
                if (($sibling['label'] ?? null) === ($item['label'] ?? null)) {
                    continue;
                }
                $siblingParams = $sibling['params'] ?? [];
                if (empty($siblingParams)) {
                    continue;
                }
                $siblingMatches = true;
                foreach ($siblingParams as $key => $value) {
                    if ((string) request($key) !== (string) $value) {
                        $siblingMatches = false;
                        break;
                    }
                }
                if ($siblingMatches) {
                    return false;
                }
            }
        }

        return true;
    };

    $defaultOpenGroups = [];
    foreach ($menuSections as $section) {
        if (isset($section['group'])) {
            $defaultOpenGroups[$section['group']] = collect($section['items'])->contains(
                fn ($item) => $isMenuItemActive($item)
            );
        }
    }
@endphp

<aside class="panel-sidebar panel-sidebar-vertical fixed inset-y-0 left-0 z-50 w-72 transform transition-transform duration-200 lg:translate-x-0 overflow-hidden"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
    <div class="panel-sidebar-glow-bottom" aria-hidden="true"></div>
    <div class="panel-sidebar-inner flex flex-col h-full">
        <div class="panel-sidebar-header flex items-center gap-3 px-6 py-5">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $isPlatformPanel ? 'from-slate-600 to-slate-800' : ($isStudentPanel || $role === 'learner' ? 'from-emerald-500 to-teal-600' : 'from-indigo-500 via-violet-500 to-purple-600') }} flex items-center justify-center shadow-lg {{ $isPlatformPanel ? 'shadow-slate-500/25' : ($isStudentPanel || $role === 'learner' ? 'shadow-emerald-500/25' : 'shadow-indigo-500/30') }} ring-2 ring-white/60">
                <span class="text-white font-bold text-lg">{{ $isPlatformPanel ? 'P' : ($isStudentPanel || $role === 'learner' ? 'S' : 'L') }}</span>
            </div>
            <div>
                <h1 class="text-slate-800 font-bold text-lg tracking-tight">Learnyst</h1>
                <p class="text-xs font-semibold panel-sidebar-brand-sub">{{ $panelTitle }}</p>
            </div>
        </div>

        <nav class="panel-sidebar-nav flex-1 overflow-y-auto py-4 px-3 space-y-0.5" x-data="{ openGroups: @js($defaultOpenGroups) }">
            @if($role === 'super-admin')
                @foreach($menuSections as $section)
                    @if(isset($section['route']))
                        @php $isActive = $isMenuItemActive($section); @endphp
                        <a href="{{ route($section['route'], $section['params'] ?? []) }}"
                           class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ $isActive ? 'active' : 'text-slate-600' }}">
                            <span class="sidebar-icon-wrap">
                                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $section['icon'] }}"/></svg>
                            </span>
                            {{ $section['label'] }}
                        </a>
                    @elseif(isset($section['group']))
                        @php
                            $groupName = $section['group'];
                            $groupActive = collect($section['items'])->contains(fn ($i) => $isMenuItemActive($i));
                        @endphp
                        <div class="mb-0.5">
                            <button type="button"
                                    @click="openGroups['{{ $groupName }}'] = !openGroups['{{ $groupName }}']"
                                    class="panel-sidebar-group-btn w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ $groupActive ? 'panel-sidebar-group-active' : 'text-slate-600' }}">
                                <span class="sidebar-icon-wrap">
                                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $section['icon'] }}"/></svg>
                                </span>
                                <span class="flex-1 text-left">{{ $groupName }}</span>
                                <svg class="w-4 h-4 transition-transform duration-200 text-slate-400"
                                     :class="openGroups['{{ $groupName }}'] ? 'rotate-180' : ''"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="openGroups['{{ $groupName }}']"
                                 x-cloak
                                 x-transition
                                 class="ml-3 mt-1 space-y-0.5 border-l-2 panel-sidebar-sub-border pl-3">
                                @foreach($section['items'] as $item)
                                    <a href="{{ route($item['route'], $item['params'] ?? []) }}"
                                       class="panel-sidebar-sub-link flex items-center justify-between gap-2 px-2.5 py-1.5 text-sm rounded-lg transition {{ $isMenuItemActive($item) ? 'panel-sidebar-sub-active' : '' }}">
                                        <span>{{ $item['label'] }}</span>
                                        @if(!empty($item['badge']))
                                            <span class="shrink-0 px-1.5 py-0.5 text-[10px] font-semibold leading-none rounded-full {{ is_numeric($item['badge']) ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-700' }}">{{ $item['badge'] }}</span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            @elseif(in_array($role, ['admin', 'sub-admin']))
                @foreach($menuSections as $section)
                    @if(isset($section['route']))
                        @php $isActive = $isMenuItemActive($section); @endphp
                        <a href="{{ route($section['route'], $section['params'] ?? []) }}"
                           class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ $isActive ? 'active' : 'text-slate-600' }}">
                            <span class="sidebar-icon-wrap">
                                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $section['icon'] }}"/></svg>
                            </span>
                            {{ $section['label'] }}
                        </a>
                    @elseif(isset($section['group']))
                        @php
                            $groupName = $section['group'];
                            $groupActive = collect($section['items'])->contains(fn ($i) => $isMenuItemActive($i));
                        @endphp
                        <div class="mb-0.5">
                            <button type="button"
                                    @click="openGroups['{{ $groupName }}'] = !openGroups['{{ $groupName }}']"
                                    class="panel-sidebar-group-btn w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ $groupActive ? 'panel-sidebar-group-active' : 'text-slate-600' }}">
                                <span class="sidebar-icon-wrap">
                                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $section['icon'] }}"/></svg>
                                </span>
                                <span class="flex-1 text-left">{{ $groupName }}</span>
                                <svg class="w-4 h-4 transition-transform duration-200 text-slate-400"
                                     :class="openGroups['{{ $groupName }}'] ? 'rotate-180' : ''"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="openGroups['{{ $groupName }}']"
                                 x-cloak
                                 x-transition
                                 class="ml-3 mt-1 space-y-0.5 border-l-2 panel-sidebar-sub-border pl-3">
                                @foreach($section['items'] as $item)
                                    <a href="{{ route($item['route'], $item['params'] ?? []) }}"
                                       class="panel-sidebar-sub-link flex items-center justify-between gap-2 px-2.5 py-1.5 text-sm rounded-lg transition {{ $isMenuItemActive($item) ? 'panel-sidebar-sub-active' : '' }}">
                                        <span>{{ $item['label'] }}</span>
                                        @if(!empty($item['badge']))
                                            <span class="shrink-0 px-1.5 py-0.5 text-[10px] font-semibold leading-none rounded-full {{ is_numeric($item['badge']) ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-700' }}">{{ $item['badge'] }}</span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            @else
                @php $menu = $role === 'instructor' ? $instructorMenu : $learnerMenu; @endphp
                @foreach($menu as $item)
                    @php $isActive = $isMenuItemActive($item); @endphp
                    <a href="{{ route($item['route'], $item['params'] ?? []) }}"
                       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ $isActive ? 'active' : 'text-slate-600' }}">
                        <span class="sidebar-icon-wrap">
                            <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $item['icon'] }}"/></svg>
                        </span>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            @endif
        </nav>

        <div class="panel-sidebar-footer p-4">
            <div class="panel-sidebar-user flex items-center gap-3 px-2 py-2 rounded-xl">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-400 to-violet-500 flex items-center justify-center text-sm font-semibold text-white overflow-hidden shrink-0">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 truncate">{{ $user->name }}</p>
                    <p class="text-xs text-slate-500 truncate">{{ $user->email }}</p>
                </div>
            </div>
        </div>
    </div>
</aside>

@if($sidebarLayout === 'horizontal' && in_array($role, ['admin', 'sub-admin', 'super-admin']))
<header class="panel-sidebar-horizontal" x-data="{ mobileOpen: false }">
    <div class="flex items-center gap-3 px-4 h-16 border-b border-white/40">
        <button type="button" @click="mobileOpen = !mobileOpen" class="lg:hidden p-2 rounded-lg text-slate-500 hover:bg-white/50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 via-violet-500 to-purple-600 flex items-center justify-center shadow-md ring-2 ring-white/60 shrink-0">
            <span class="text-white font-bold">L</span>
        </div>
        <div class="min-w-0">
            <p class="text-sm font-bold text-slate-800 truncate">Learnyst</p>
            <p class="text-[10px] font-semibold panel-sidebar-brand-sub truncate">{{ $panelTitle }}</p>
        </div>
        <div class="ml-auto flex items-center gap-2 shrink-0">
            <div class="hidden sm:flex items-center gap-2 px-2 py-1.5 rounded-lg panel-sidebar-user max-w-[180px]">
                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-400 to-violet-500 flex items-center justify-center text-xs font-semibold text-white overflow-hidden shrink-0">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    @endif
                </div>
                <span class="text-xs font-semibold text-slate-700 truncate">{{ $user->name }}</span>
            </div>
        </div>
    </div>
    <div class="panel-sidebar-horizontal-nav px-3 py-2"
         :class="mobileOpen ? '' : 'hidden lg:block'">
        <div class="panel-sidebar-horizontal-nav-scroll">
            <div class="flex items-center gap-1.5 min-w-max"
                 :class="mobileOpen ? 'flex-wrap' : ''">
                @foreach($menuSections as $menuIndex => $section)
                    @if(isset($section['route']))
                        @php $isActive = $isMenuItemActive($section); @endphp
                        <a href="{{ route($section['route'], $section['params'] ?? []) }}"
                           class="shrink-0 px-3 py-2 rounded-xl text-xs font-semibold transition {{ $isActive ? 'panel-sidebar-group-active shadow-sm' : 'text-slate-600 hover:bg-white/55' }}">
                            {{ $section['label'] }}
                        </a>
                    @elseif(isset($section['group']))
                        @php
                            $groupName = $section['group'];
                            $groupActive = collect($section['items'])->contains(fn ($i) => $isMenuItemActive($i));
                        @endphp
                        <div class="relative shrink-0"
                             x-data="{
                                 open: false,
                                 menuStyle: {},
                                 updateMenuPosition() {
                                     if (!this.$refs.menuBtn) return;
                                     const rect = this.$refs.menuBtn.getBoundingClientRect();
                                     this.menuStyle = {
                                         top: (rect.bottom + 6) + 'px',
                                         left: rect.left + 'px',
                                     };
                                 },
                                 toggleMenu() {
                                     if (this.open) {
                                         this.open = false;
                                         return;
                                     }
                                     this.$dispatch('close-horizontal-menu');
                                     this.updateMenuPosition();
                                     this.open = true;
                                 },
                             }"
                             @keydown.escape.window="open = false"
                             @close-horizontal-menu.window="open = false"
                             @click.outside="open = false"
                             @scroll.window="if (open) updateMenuPosition()"
                             @resize.window="if (open) updateMenuPosition()">
                            <button type="button"
                                    x-ref="menuBtn"
                                    @click.stop="toggleMenu()"
                                    class="px-3 py-2 rounded-xl text-xs font-semibold transition inline-flex items-center gap-1 {{ $groupActive ? 'panel-sidebar-group-active shadow-sm' : 'text-slate-600 hover:bg-white/55' }}">
                                {{ $groupName }}
                                <svg class="w-3.5 h-3.5 transition-transform duration-200"
                                     :class="open ? 'rotate-180' : ''"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open"
                                 x-cloak
                                 x-bind:style="menuStyle"
                                 class="panel-sidebar-horizontal-dropdown fixed min-w-[220px] max-h-72 overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-xl py-1">
                                @foreach($section['items'] as $item)
                                    @if(!empty($item['hidden'])) @continue @endif
                                    <a href="{{ route($item['route'], $item['params'] ?? []) }}"
                                       @click="open = false"
                                       class="block px-3 py-2 text-xs font-medium {{ $isMenuItemActive($item) ? 'text-indigo-700 bg-indigo-50' : 'text-slate-600 hover:bg-slate-50' }}">
                                        {{ $item['label'] }}
                                        @if(!empty($item['badge']))
                                            <span class="ml-1 px-1.5 py-0.5 text-[10px] font-semibold rounded-full {{ is_numeric($item['badge']) ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-700' }}">{{ $item['badge'] }}</span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</header>
@endif

<div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak class="fixed inset-0 bg-slate-900/20 backdrop-blur-sm z-40 lg:hidden"></div>
