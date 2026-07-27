@php
    use App\Services\CompanyService;

    $menuUser = $menuUser ?? auth()->user();
    $showCompanyBranding = $showCompanyBranding ?? request()->is('company', 'company/*');
    $displayName = $menuUser?->name ?? 'User';
    $displayAvatar = $menuUser?->avatar;
    $displayInitial = strtoupper(substr($displayName, 0, 1));

    if ($showCompanyBranding && $menuUser) {
        try {
            $menuCompany = CompanyService::resolveForUser($menuUser);
            if ($menuCompany?->name) {
                $displayName = $menuCompany->name;
            }
            if ($menuCompany && method_exists($menuCompany, 'logoUrl') && $menuCompany->logoUrl()) {
                $displayAvatar = $menuCompany->logoUrl();
            }
        } catch (\Throwable $e) {
            // keep user defaults
        }
    }

    $variant = $variant ?? 'topbar';

    if ($variant === 'sidebar') {
        $showCompanyBranding = false;
        $displayName = $menuUser?->name ?? 'User';
        $displayAvatar = $menuUser?->avatar;
        $displayInitial = strtoupper(substr($displayName, 0, 1));
    }

    $triggerClass = match ($variant) {
        'horizontal' => 'px-2 py-1.5 panel-sidebar-user max-w-[220px]',
        'sidebar' => 'panel-sidebar-user w-full px-2 py-2 rounded-xl',
        default => 'px-2 py-1.5 hover:bg-slate-50 border border-transparent hover:border-slate-200',
    };

    $avatarClass = match (true) {
        ($variant === 'horizontal' || $variant === 'sidebar') && ($panelSidebarFlat ?? false) => 'bg-[var(--theme-accent,#0d9488)]',
        $variant === 'horizontal' => 'bg-gradient-to-br from-indigo-400 to-violet-500',
        default => 'bg-[var(--theme-accent,#0d9488)]',
    };

    $dropdownClass = $variant === 'sidebar'
        ? 'panel-user-menu-dropdown absolute left-0 bottom-full mb-2 w-full min-w-[14rem] rounded-xl border border-slate-200 bg-white shadow-lg py-1 z-[120]'
        : 'panel-user-menu-dropdown absolute right-0 mt-2 w-56 rounded-xl border border-slate-200 bg-white shadow-lg py-1 z-[120]';
@endphp

<div class="relative {{ $variant === 'sidebar' ? 'w-full' : '' }}" x-data="{ profileOpen: false }" @keydown.escape.window="profileOpen = false">
    <button type="button"
            @click="profileOpen = !profileOpen"
            @click.outside="profileOpen = false"
            class="panel-user-menu-trigger flex items-center gap-2 rounded-lg transition {{ $triggerClass }}"
            :aria-expanded="profileOpen"
            aria-haspopup="true">
        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold text-white overflow-hidden shrink-0 {{ $avatarClass }} {{ $variant === 'sidebar' ? 'w-9 h-9 text-sm' : '' }}">
            @if($displayAvatar)
                <img src="{{ str_starts_with($displayAvatar, 'http') || str_starts_with($displayAvatar, '/') ? $displayAvatar : asset('storage/' . ltrim($displayAvatar, '/')) }}" alt="" class="w-full h-full object-cover">
            @else
                {{ $displayInitial }}
            @endif
        </div>
        <div class="{{ $variant === 'sidebar' ? 'flex-1' : 'hidden sm:block' }} text-left min-w-0">
            <p class="text-sm font-semibold text-slate-800 truncate leading-tight" style="font-family: Inter, sans-serif;">{{ $displayName }}</p>
            @if($variant === 'sidebar' && $menuUser?->email)
                <p class="text-xs text-slate-500 truncate leading-tight">{{ $menuUser->email }}</p>
            @elseif($showCompanyBranding && $menuUser?->name && $displayName !== $menuUser->name)
                <p class="text-[11px] text-slate-500 truncate leading-tight">{{ $menuUser->name }}</p>
            @elseif($menuUser?->role?->name)
                <p class="text-[11px] text-slate-500 truncate leading-tight">{{ $menuUser->role->name }}</p>
            @endif
        </div>
        <svg class="w-4 h-4 text-slate-400 shrink-0 {{ $variant === 'sidebar' ? '' : 'hidden sm:block' }} transition-transform duration-200"
             :class="profileOpen ? 'rotate-180' : ''"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div x-show="profileOpen"
         x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-1"
         class="{{ $dropdownClass }}"
         style="font-family: Inter, sans-serif;">
        <div class="px-4 py-3 border-b border-slate-100 {{ $variant === 'sidebar' ? '' : 'sm:hidden' }}">
            <p class="text-sm font-semibold text-slate-800 truncate">{{ $displayName }}</p>
            @if($menuUser?->email)
                <p class="text-xs text-slate-500 truncate">{{ $menuUser->email }}</p>
            @endif
        </div>
        <a href="{{ route('profile.edit') }}"
           @click="profileOpen = false"
           class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition">
            <svg class="w-4 h-4 shrink-0" style="color: var(--theme-accent, #0d9488)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            My Profile
        </a>
        <a href="{{ route('profile.edit') }}#change-password"
           @click="profileOpen = false"
           class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition">
            <svg class="w-4 h-4 shrink-0" style="color: var(--theme-accent, #0d9488)" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            Change Password
        </a>
        <div class="border-t border-slate-100 my-1"></div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition text-left">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Logout
            </button>
        </form>
    </div>
</div>
