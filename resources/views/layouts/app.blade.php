<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'Learnyst') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        },
                        panel: {
                            bg: '#f0f4fa',
                            card: '#ffffff',
                            border: '#e2e8f0',
                        }
                    },
                    boxShadow: {
                        soft: '0 1px 2px rgba(15, 23, 42, 0.04), 0 8px 24px rgba(15, 23, 42, 0.06)',
                        sidebar: '4px 0 24px rgba(15, 23, 42, 0.04)',
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }

        body.panel-app {
            background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 50%, #f0f9ff 100%);
            color: #334155;
        }

        .glass-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04), 0 8px 24px rgba(15, 23, 42, 0.05);
        }

        .panel-sidebar {
            position: relative;
            background-color: #e0e7ff;
            background-image: var(--panel-sidebar-mesh), var(--panel-sidebar-bg, linear-gradient(168deg, #dbeafe 0%, #e0e7ff 22%, #ddd6fe 48%, #ede9fe 72%, #e0f2fe 100%));
            border-right: 1px solid var(--panel-sidebar-border, rgba(129, 140, 248, 0.45));
            box-shadow: 4px 0 32px rgba(99, 102, 241, 0.12), inset -1px 0 0 rgba(255, 255, 255, 0.4);
        }
        .panel-sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 4px;
            background: var(--panel-sidebar-stripe, linear-gradient(180deg, #6366f1 0%, #8b5cf6 50%, #3b82f6 100%));
            z-index: 2;
            pointer-events: none;
        }
        .panel-sidebar::after {
            content: '';
            position: absolute;
            top: -60px;
            right: -50px;
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, var(--panel-sidebar-glow1, rgba(99, 102, 241, 0.38)) 0%, transparent 65%);
            pointer-events: none;
            z-index: 0;
        }
        .panel-sidebar > .panel-sidebar-inner {
            position: relative;
            z-index: 1;
        }
        .panel-sidebar-glow-bottom {
            position: absolute;
            bottom: 60px;
            left: -50px;
            width: 180px;
            height: 180px;
            background: radial-gradient(circle, var(--panel-sidebar-glow2, rgba(139, 92, 246, 0.3)) 0%, transparent 68%);
            pointer-events: none;
            z-index: 0;
        }
        .panel-sidebar-header {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.72) 0%, rgba(255, 255, 255, 0.38) 100%);
            border-bottom: 1px solid var(--panel-sidebar-border, rgba(129, 140, 248, 0.35));
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .panel-sidebar-footer {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0.45) 100%);
            border-top: 1px solid var(--panel-sidebar-border, rgba(129, 140, 248, 0.35));
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        .panel-sidebar-user {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.92) 0%, rgba(255, 255, 255, 0.65) 100%);
            border: 1px solid rgba(255, 255, 255, 0.7);
            box-shadow: 0 4px 16px rgba(99, 102, 241, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
        }
        .panel-sidebar-nav {
            scrollbar-width: thin;
            scrollbar-color: var(--panel-sidebar-accent, #6366f1) transparent;
        }
        .panel-sidebar-nav::-webkit-scrollbar { width: 5px; }
        .panel-sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .panel-sidebar-nav::-webkit-scrollbar-thumb {
            background: var(--panel-sidebar-accent-gradient, linear-gradient(180deg, #6366f1, #8b5cf6));
            border-radius: 9999px;
        }

        .sidebar-icon-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            border-radius: 0.625rem;
            flex-shrink: 0;
            transition: background 0.2s, box-shadow 0.2s, color 0.2s;
        }
        .sidebar-link {
            position: relative;
            border-left: 3px solid transparent;
        }
        .sidebar-link:hover:not(.active) {
            background: var(--panel-sidebar-hover-bg, linear-gradient(90deg, rgba(255,255,255,0.55) 0%, rgba(238,242,255,0.45) 100%));
            color: var(--panel-sidebar-accent-dark, #4338ca) !important;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.06);
        }
        .sidebar-link:hover:not(.active) .sidebar-icon-wrap {
            background: rgba(255, 255, 255, 0.55);
            color: var(--panel-sidebar-accent, #6366f1);
        }
        .sidebar-link.active {
            background: var(--panel-sidebar-active-bg, linear-gradient(90deg, rgba(99,102,241,0.22) 0%, rgba(139,92,246,0.14) 100%));
            border-left-color: var(--panel-sidebar-accent, #6366f1);
            color: var(--panel-sidebar-accent-dark, #4338ca) !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6), 0 4px 14px rgba(99, 102, 241, 0.12);
        }
        .sidebar-link.active .sidebar-icon-wrap {
            background: var(--panel-sidebar-accent-gradient, linear-gradient(135deg, #6366f1, #8b5cf6));
            color: #fff;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);
        }

        .panel-sidebar-group-btn:hover:not(.panel-sidebar-group-active) {
            background: var(--panel-sidebar-hover-bg);
            color: var(--panel-sidebar-accent-dark, #4338ca) !important;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.06);
        }
        .panel-sidebar-group-active {
            background: var(--panel-sidebar-active-bg);
            color: var(--panel-sidebar-accent-dark, #4338ca) !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.5), 0 2px 10px rgba(99, 102, 241, 0.1);
        }
        .panel-sidebar-group-active .sidebar-icon-wrap {
            background: var(--panel-sidebar-accent-gradient);
            color: #fff;
            box-shadow: 0 3px 10px rgba(99, 102, 241, 0.3);
        }
        .panel-sidebar-sub-active {
            background: var(--panel-sidebar-active-bg);
            color: var(--panel-sidebar-accent-dark, #4338ca) !important;
            font-weight: 600;
            box-shadow: inset 3px 0 0 var(--panel-sidebar-accent, #6366f1);
        }
        .panel-sidebar-sub-border {
            border-left-color: var(--panel-sidebar-accent, #818cf8) !important;
            opacity: 0.72;
        }
        .panel-sidebar-sub-link {
            color: var(--panel-sidebar-accent-dark, #3730a3);
            font-weight: 600;
            text-shadow: 0 1px 0 rgba(255, 255, 255, 0.45);
        }
        .panel-sidebar-sub-link:hover:not(.panel-sidebar-sub-active) {
            background: var(--panel-sidebar-hover-bg);
            color: var(--panel-sidebar-accent-dark, #312e81) !important;
        }
        .panel-sidebar-brand-sub {
            color: var(--panel-sidebar-accent, #6366f1);
        }

        .panel-input {
            width: 100%;
            padding: 0.625rem 1rem;
            border-radius: 0.75rem;
            background: #fff;
            border: 1px solid #e2e8f0;
            color: #1e293b;
            font-size: 0.875rem;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }
        .panel-input:focus {
            outline: none;
            border-color: #818cf8;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }
        .panel-input::placeholder { color: #94a3b8; }

        .panel-select {
            padding: 0.625rem 1rem;
            border-radius: 0.75rem;
            background: #fff;
            border: 1px solid #e2e8f0;
            color: #334155;
            font-size: 0.875rem;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }
        .panel-select:focus {
            outline: none;
            border-color: #818cf8;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        .panel-btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #475569;
            background: #fff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
            transition: all 0.15s;
        }
        .panel-btn-secondary:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .panel-btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #fff;
            background: linear-gradient(to right, #4f46e5, #7c3aed);
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.25);
            transition: all 0.15s;
        }
        .panel-btn-primary:hover {
            background: linear-gradient(to right, #6366f1, #8b5cf6);
        }

        .panel-table thead {
            background: #f8fafc;
        }

        .panel-table thead th {
            color: #64748b;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .panel-table tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.15s;
        }

        .panel-table tbody tr:hover {
            background: #f8fafc;
        }

        .panel-table tbody td {
            color: #475569;
        }

        /* Pagination light theme */
        .panel-app nav[role="navigation"] span, .panel-app nav[role="navigation"] a {
            color: #64748b !important;
        }
        .panel-app nav[role="navigation"] a.bg-gray-800,
        .panel-app nav[role="navigation"] span.bg-gray-800 {
            background: #6366f1 !important;
            color: white !important;
        }

        /* Page skeleton — critical path to avoid content flash */
        .panel-skeleton-block {
            background: linear-gradient(90deg, #eef2f7 0%, #f8fafc 40%, #eef2f7 80%);
            background-size: 200% 100%;
            animation: panelSkeletonShimmer 1.35s ease-in-out infinite;
        }
        @keyframes panelSkeletonShimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        .panel-page-skeleton { display: block; }
        .panel-page-content { display: none; }
        body.panel-page-ready .panel-page-skeleton { display: none; }
        body.panel-page-ready .panel-page-content { display: block; }

        /* Horizontal sidebar layout */
        .panel-sidebar-horizontal { display: none; }
        .panel-layout-horizontal .panel-sidebar-vertical { display: none !important; }
        .panel-layout-horizontal .panel-sidebar-horizontal {
            display: block;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            background-color: #e0e7ff;
            background-image: var(--panel-sidebar-mesh), var(--panel-sidebar-bg);
            border-bottom: 1px solid var(--panel-sidebar-border);
            box-shadow: 0 4px 24px rgba(99, 102, 241, 0.1);
            overflow: visible;
        }
        .panel-layout-horizontal .panel-main-horizontal {
            margin-left: 0 !important;
            padding-top: 7.5rem;
        }
        .panel-sidebar-horizontal-nav {
            scrollbar-width: thin;
            overflow: visible;
        }
        .panel-sidebar-horizontal-nav-scroll {
            overflow-x: auto;
            overflow-y: visible;
            scrollbar-width: thin;
        }
        .panel-sidebar-horizontal-nav-scroll::-webkit-scrollbar { height: 4px; }
        .panel-sidebar-horizontal-nav-scroll::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.45);
            border-radius: 9999px;
        }
        .panel-sidebar-horizontal-dropdown {
            position: fixed;
            z-index: 100;
            scrollbar-width: thin;
            scrollbar-color: var(--panel-sidebar-accent, #6366f1) transparent;
        }
        .panel-sidebar-horizontal-dropdown[x-cloak] {
            display: none !important;
        }
        .panel-sidebar-horizontal-dropdown::-webkit-scrollbar {
            width: 5px;
        }
        .panel-sidebar-horizontal-dropdown::-webkit-scrollbar-track {
            background: transparent;
            margin: 6px 0;
        }
        .panel-sidebar-horizontal-dropdown::-webkit-scrollbar-thumb {
            background: var(--panel-sidebar-accent-gradient, linear-gradient(180deg, #6366f1 0%, #8b5cf6 100%));
            border-radius: 9999px;
            border: 1px solid rgba(255, 255, 255, 0.65);
            box-shadow: 0 1px 4px rgba(99, 102, 241, 0.25);
        }
        .panel-sidebar-horizontal-dropdown::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #4f46e5 0%, #7c3aed 100%);
        }
        .panel-sidebar-horizontal-dropdown::-webkit-scrollbar-corner {
            background: transparent;
        }

        /* Sidebar skeleton during page load / navigation */
        .panel-sidebar-skeleton { display: block; }
        body.panel-page-ready .panel-sidebar-skeleton { display: none; }
        body:not(.panel-page-ready) .panel-sidebar-vertical,
        body:not(.panel-page-ready) .panel-sidebar-horizontal {
            visibility: hidden;
            pointer-events: none;
        }
        .panel-sidebar-skeleton-horizontal { height: 7.5rem; }
    </style>
    @stack('styles')
</head>
@php
    $panelSidebarSettings = \App\Services\SidebarSettingsService::defaults();
    if (auth()->check() && request()->is('company', 'company/*')) {
        try {
            $panelSidebarCompany = \App\Services\CompanyService::resolveForUser(auth()->user());
            $panelSidebarSettings = \App\Services\SidebarSettingsService::forCompany($panelSidebarCompany);
        } catch (\Throwable $e) {
            // keep defaults
        }
    }
    $panelSidebarCss = \App\Services\SidebarSettingsService::cssVariables($panelSidebarSettings);
    $panelSidebarHorizontal = ($panelSidebarSettings['layout'] ?? 'vertical') === 'horizontal';
@endphp
<body class="h-full panel-app font-sans antialiased {{ $panelSidebarHorizontal ? 'panel-layout-horizontal' : 'panel-layout-vertical' }}"
      style="{{ $panelSidebarCss }}"
      x-data="{ sidebarOpen: false, deleteModal: false, deleteForm: null }">
    @if($panelSidebarHorizontal)
        <x-admin.sidebar-skeleton-horizontal />
    @else
        <x-admin.sidebar-skeleton-vertical />
    @endif

    <div class="flex h-full min-h-screen {{ $panelSidebarHorizontal ? 'flex-col' : '' }}">
        @include('layouts.partials.sidebar')

        <div class="flex-1 flex flex-col min-w-0 panel-main-wrap {{ $panelSidebarHorizontal ? 'panel-main-horizontal' : 'lg:ml-72' }}">
            @include('layouts.partials.topbar')

            <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-auto relative">
                @include('layouts.partials.alerts')

                <div class="panel-page-skeleton">
                    <x-admin.page-skeleton />
                </div>

                <div class="panel-page-content">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @include('layouts.partials.delete-modal')
    @include('layouts.partials.toast')
    @include('layouts.partials.page-skeleton-loader')
    @include('layouts.partials.login-greeting')

    @stack('scripts')
</body>
</html>
