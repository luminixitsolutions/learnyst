@php
    use App\Services\PermissionService;

    $role = auth()->user()->role?->slug ?? 'admin';
    $user = auth()->user();
    $isPlatformPanel = request()->is('admin', 'admin/*');
    $isCompanyPanel = request()->is('company', 'company/*');

    $instructorMenu = [
        ['label' => 'Dashboard', 'route' => 'instructor.dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
    ];

    $learnerMenu = [
        ['label' => 'Dashboard', 'route' => 'learner.dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ['label' => 'My Courses', 'route' => 'learner.courses.index', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
        ['label' => 'Communities', 'route' => 'learner.communities.index', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
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
            return PermissionService::canAccessRoute($user, $item['permission'] ?? null);
        })->values()->all();
        if (empty($items)) {
            return null;
        }
        $section['items'] = $items;
        return $section;
    })->filter()->values()->all();

    $menuSections = $isPlatformPanel ? $platformSections : $companySections;
    $panelTitle = $isPlatformPanel ? 'Platform Admin' : 'Company Panel';

    $defaultOpenGroups = [];
    foreach ($menuSections as $section) {
        if (isset($section['group'])) {
            $defaultOpenGroups[$section['group']] = collect($section['items'])->contains(
                fn ($item) => request()->routeIs($item['route'] . '*')
            );
        }
    }
@endphp

<aside class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-slate-200/80 shadow-sidebar transform transition-transform duration-200 lg:translate-x-0"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
    <div class="flex flex-col h-full">
        <div class="flex items-center gap-3 px-6 py-5 border-b border-slate-100">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $isPlatformPanel ? 'from-slate-700 to-slate-900' : 'from-indigo-500 to-violet-600' }} flex items-center justify-center shadow-lg {{ $isPlatformPanel ? 'shadow-slate-500/30' : 'shadow-indigo-500/30' }}">
                <span class="text-white font-bold text-lg">{{ $isPlatformPanel ? 'P' : 'L' }}</span>
            </div>
            <div>
                <h1 class="text-slate-800 font-bold text-lg tracking-tight">Learnyst</h1>
                <p class="text-xs text-slate-500">{{ $panelTitle }}</p>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5" x-data="{ openGroups: @js($defaultOpenGroups) }">
            @if($role === 'super-admin')
                @foreach($menuSections as $section)
                    @if(isset($section['route']))
                        @php $isActive = request()->routeIs($section['route'] . '*'); @endphp
                        <a href="{{ route($section['route']) }}"
                           class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ $isActive ? 'active' : 'text-slate-600 hover:text-indigo-600 hover:bg-indigo-50/70' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $section['icon'] }}"/></svg>
                            {{ $section['label'] }}
                        </a>
                    @elseif(isset($section['group']))
                        @php
                            $groupName = $section['group'];
                            $groupActive = collect($section['items'])->contains(fn($i) => request()->routeIs($i['route'] . '*'));
                        @endphp
                        <div class="mb-0.5">
                            <button type="button"
                                    @click="openGroups['{{ $groupName }}'] = !openGroups['{{ $groupName }}']"
                                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ $groupActive ? 'text-indigo-600 bg-indigo-50/60' : 'text-slate-600 hover:text-indigo-600 hover:bg-indigo-50/70' }}">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $section['icon'] }}"/></svg>
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
                                 class="ml-3 mt-1 space-y-0.5 border-l-2 border-indigo-100 pl-3">
                                @foreach($section['items'] as $item)
                                    <a href="{{ route($item['route'], $item['params'] ?? []) }}"
                                       class="flex items-center justify-between gap-2 px-2.5 py-1.5 text-xs font-medium rounded-lg {{ request()->routeIs($item['route'] . '*') ? 'text-indigo-600 bg-indigo-50' : 'text-slate-500 hover:text-indigo-600 hover:bg-indigo-50/50' }}">
                                        <span>{{ $item['label'] }}</span>
                                        @if(!empty($item['badge']))
                                            <span class="shrink-0 px-1.5 py-0.5 text-[10px] font-semibold leading-none rounded-full bg-emerald-100 text-emerald-700">{{ $item['badge'] }}</span>
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
                        @php $isActive = request()->routeIs($section['route'] . '*'); @endphp
                        <a href="{{ route($section['route']) }}"
                           class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ $isActive ? 'active' : 'text-slate-600 hover:text-indigo-600 hover:bg-indigo-50/70' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $section['icon'] }}"/></svg>
                            {{ $section['label'] }}
                        </a>
                    @elseif(isset($section['group']))
                        @php
                            $groupName = $section['group'];
                            $groupActive = collect($section['items'])->contains(fn($i) => request()->routeIs($i['route'] . '*'));
                        @endphp
                        <div class="mb-0.5">
                            <button type="button"
                                    @click="openGroups['{{ $groupName }}'] = !openGroups['{{ $groupName }}']"
                                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ $groupActive ? 'text-indigo-600 bg-indigo-50/60' : 'text-slate-600 hover:text-indigo-600 hover:bg-indigo-50/70' }}">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $section['icon'] }}"/></svg>
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
                                 class="ml-3 mt-1 space-y-0.5 border-l-2 border-indigo-100 pl-3">
                                @foreach($section['items'] as $item)
                                    <a href="{{ route($item['route'], $item['params'] ?? []) }}"
                                       class="flex items-center justify-between gap-2 px-2.5 py-1.5 text-xs font-medium rounded-lg {{ request()->routeIs($item['route'] . '*') ? 'text-indigo-600 bg-indigo-50' : 'text-slate-500 hover:text-indigo-600 hover:bg-indigo-50/50' }}">
                                        <span>{{ $item['label'] }}</span>
                                        @if(!empty($item['badge']))
                                            <span class="shrink-0 px-1.5 py-0.5 text-[10px] font-semibold leading-none rounded-full bg-emerald-100 text-emerald-700">{{ $item['badge'] }}</span>
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
                    @php $isActive = request()->routeIs($item['route'] . '*'); @endphp
                    <a href="{{ route($item['route']) }}"
                       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ $isActive ? 'active' : 'text-slate-600 hover:text-indigo-600 hover:bg-indigo-50/70' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $item['icon'] }}"/></svg>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            @endif
        </nav>

        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            <div class="flex items-center gap-3 px-2 py-2 rounded-xl bg-white border border-slate-100 shadow-sm">
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

<div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak class="fixed inset-0 bg-slate-900/20 backdrop-blur-sm z-40 lg:hidden"></div>
