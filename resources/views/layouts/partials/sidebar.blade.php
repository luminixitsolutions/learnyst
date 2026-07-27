@php
    use App\Services\PermissionService;
    use App\Services\CompanyService;
    use App\Services\SidebarSettingsService;

    $role = auth()->user()->role?->slug ?? 'admin';
    $user = auth()->user();
    $isPlatformPanel = request()->is('admin', 'admin/*');
    $isCompanyPanel = request()->is('company', 'company/*');
    $isStudentPanel = request()->is('learner', 'learner/*', 'alumni', 'alumni/*', 'parent', 'parent/*');

    $instructorMenu = [
        ['label' => 'Dashboard', 'route' => 'instructor.dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
    ];

    $studentPanelRoles = ['learner', 'alumni', 'parent'];

    $learnerMenu = config('panel-menus.learner', []);
    $alumniMenu = config('panel-menus.alumni', []);
    $parentMenu = config('panel-menus.parent', []);

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
    $panelSidebarFlat = SidebarSettingsService::isFlatTheme($sidebarSettings);
    $panelTitle = $isPlatformPanel ? 'Platform Admin' : match ($role) {
        'alumni' => 'Alumni Portal',
        'parent' => 'Parent Portal',
        default => ($isStudentPanel || in_array($role, $studentPanelRoles, true) ? 'Student Panel' : 'Institute Panel'),
    };

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
            <div class="panel-sidebar-logo-mark w-10 h-10 rounded-xl {{ $panelSidebarFlat ? '' : 'bg-gradient-to-br ' . ($isPlatformPanel ? 'from-slate-600 to-slate-800' : ($isStudentPanel || in_array($role, $studentPanelRoles, true) ? 'from-emerald-500 to-teal-600' : 'from-indigo-500 via-violet-500 to-purple-600')) }} flex items-center justify-center {{ $panelSidebarFlat ? '' : 'shadow-lg ring-2 ring-white/60 ' . ($isPlatformPanel ? 'shadow-slate-500/25' : ($isStudentPanel || in_array($role, $studentPanelRoles, true) ? 'shadow-emerald-500/25' : 'shadow-indigo-500/30')) }}">
                <span class="text-white font-bold text-lg">{{ $isPlatformPanel ? 'P' : ($isStudentPanel || in_array($role, $studentPanelRoles, true) ? 'S' : 'L') }}</span>
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
                                @include('layouts.partials.menu-icon', ['icon' => $section['icon']])
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
                                    @include('layouts.partials.menu-icon', ['icon' => $section['icon']])
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
            @elseif(in_array($role, ['admin', 'sub-admin', 'counselor']))
                @foreach($menuSections as $section)
                    @if(isset($section['route']))
                        @php $isActive = $isMenuItemActive($section); @endphp
                        <a href="{{ route($section['route'], $section['params'] ?? []) }}"
                           class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ $isActive ? 'active' : 'text-slate-600' }}">
                            <span class="sidebar-icon-wrap">
                                @include('layouts.partials.menu-icon', ['icon' => $section['icon']])
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
                                    @include('layouts.partials.menu-icon', ['icon' => $section['icon']])
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
                @php
                    $menu = match ($role) {
                        'instructor' => $instructorMenu,
                        'alumni' => $alumniMenu,
                        'parent' => $parentMenu,
                        default => $learnerMenu,
                    };
                @endphp
                @foreach($menu as $item)
                    @php $isActive = $isMenuItemActive($item); @endphp
                    <a href="{{ route($item['route'], $item['params'] ?? []) }}"
                       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ $isActive ? 'active' : 'text-slate-600' }}">
                        <span class="sidebar-icon-wrap">
                            @include('layouts.partials.menu-icon', ['icon' => $item['icon']])
                        </span>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            @endif
        </nav>

        <div class="panel-sidebar-footer p-4">
            @include('layouts.partials.user-menu-dropdown', ['variant' => 'sidebar', 'panelSidebarFlat' => $panelSidebarFlat])
        </div>
    </div>
</aside>

@if($sidebarLayout === 'horizontal' && in_array($role, ['admin', 'sub-admin', 'counselor', 'super-admin']))
<header class="panel-sidebar-horizontal"
        x-data="{
            mobileOpen: false,
            canScrollPrev: false,
            canScrollNext: false,
            hasNavOverflow: false,
            updateNavScroll() {
                const el = this.$refs.navScroller;
                if (!el || this.mobileOpen) {
                    this.canScrollPrev = false;
                    this.canScrollNext = false;
                    this.hasNavOverflow = false;
                    return;
                }
                const edge = 8;
                this.hasNavOverflow = el.scrollWidth > el.clientWidth + edge;
                if (!this.hasNavOverflow) {
                    this.canScrollPrev = false;
                    this.canScrollNext = false;
                    return;
                }
                this.canScrollPrev = el.scrollLeft > edge;
                this.canScrollNext = el.scrollLeft + el.clientWidth < el.scrollWidth - edge;
            },
            scrollNavPrev() {
                const el = this.$refs.navScroller;
                if (!el) return;
                el.scrollBy({ left: -Math.max(220, el.clientWidth * 0.55), behavior: 'smooth' });
                setTimeout(() => this.updateNavScroll(), 320);
            },
            scrollNavNext() {
                const el = this.$refs.navScroller;
                if (!el) return;
                el.scrollBy({ left: Math.max(220, el.clientWidth * 0.55), behavior: 'smooth' });
                setTimeout(() => this.updateNavScroll(), 320);
            },
            initNavScroll() {
                this.$nextTick(() => {
                    this.updateNavScroll();
                    setTimeout(() => this.updateNavScroll(), 150);
                    setTimeout(() => this.updateNavScroll(), 600);
                });
            },
        }"
        x-init="initNavScroll(); window.addEventListener('resize', () => updateNavScroll())">
    <div class="panel-sidebar-horizontal-brand flex items-center gap-3 px-4 h-16 border-b border-white/40">
        <button type="button" @click="mobileOpen = !mobileOpen; $nextTick(() => initNavScroll())" class="lg:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <div class="panel-sidebar-logo-mark w-9 h-9 rounded-lg flex items-center justify-center shrink-0 {{ $panelSidebarFlat ? '' : 'bg-gradient-to-br from-indigo-500 via-violet-500 to-purple-600 shadow-md ring-2 ring-white/60' }}">
            <span class="text-white font-bold">L</span>
        </div>
        <div class="min-w-0">
            <p class="text-sm font-bold text-slate-800 truncate" style="font-family: Inter, sans-serif;">Learnyst</p>
            <p class="text-[10px] font-medium panel-sidebar-brand-sub truncate">{{ $panelTitle }}</p>
        </div>
        <div class="ml-auto flex items-center gap-2 shrink-0">
            @include('layouts.partials.user-menu-dropdown', ['variant' => 'horizontal', 'panelSidebarFlat' => $panelSidebarFlat])
        </div>
    </div>
    <div class="panel-sidebar-horizontal-nav py-2"
         :class="mobileOpen ? '' : 'hidden lg:block'">
        <div class="flex items-center gap-0.5 px-1 min-w-0">
            <button type="button"
                    x-show="hasNavOverflow && !mobileOpen"
                    x-cloak
                    @click="scrollNavPrev()"
                    :disabled="!canScrollPrev"
                    class="panel-nav-scroll-btn shrink-0 hidden lg:inline-flex"
                    aria-label="Scroll menu left">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <div class="panel-sidebar-horizontal-nav-scroll flex-1 min-w-0"
                 x-ref="navScroller"
                 @scroll.debounce.30ms="updateNavScroll()">
                <div class="flex items-center gap-1.5 min-w-max"
                     :class="mobileOpen ? 'flex-wrap' : ''">
                @foreach($menuSections as $menuIndex => $section)
                    @if(isset($section['route']))
                        @php $isActive = $isMenuItemActive($section); @endphp
                        <a href="{{ route($section['route'], $section['params'] ?? []) }}"
                           class="panel-nav-item shrink-0 text-sm transition {{ $isActive ? 'panel-sidebar-group-active shadow-sm' : 'panel-sidebar-horizontal-link' }}">
                            @if(!empty($section['icon']))
                                <span class="panel-nav-icon">@include('layouts.partials.menu-icon', ['icon' => $section['icon'], 'class' => 'w-full h-full'])</span>
                            @endif
                            <span>{{ $section['label'] }}</span>
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
                                    class="panel-nav-item text-sm transition inline-flex items-center {{ $groupActive ? 'panel-sidebar-group-active shadow-sm' : 'panel-sidebar-horizontal-link' }}">
                                @if(!empty($section['icon']))
                                    <span class="panel-nav-icon">@include('layouts.partials.menu-icon', ['icon' => $section['icon'], 'class' => 'w-full h-full'])</span>
                                @endif
                                <span>{{ $groupName }}</span>
                                <svg class="panel-nav-chevron transition-transform duration-200"
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
                                       class="block px-3 py-2 text-sm font-medium {{ $isMenuItemActive($item) ? 'text-teal-800 bg-teal-50' : 'text-slate-600 hover:bg-slate-50' }}">
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
            <button type="button"
                    x-show="hasNavOverflow && !mobileOpen"
                    x-cloak
                    @click="scrollNavNext()"
                    :disabled="!canScrollNext"
                    class="panel-nav-scroll-btn shrink-0 hidden lg:inline-flex"
                    aria-label="Scroll menu right">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>
</header>
@endif

<div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak class="fixed inset-0 bg-slate-900/20 backdrop-blur-sm z-40 lg:hidden"></div>
