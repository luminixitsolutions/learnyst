<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'StudyNest') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#ecfdf5',
                            100: '#ccfbf1',
                            200: '#b6dfdb',
                            400: '#7ac4be',
                            500: '#0d9488',
                            600: '#0d9488',
                            700: '#0b7970',
                            800: '#09655c',
                        },
                        panel: {
                            bg: '#f3f4f6',
                            card: '#ffffff',
                            border: '#e2e8f0',
                        },
                        indigo: {
                            50: '#ecfdf5',
                            100: '#ccfbf1',
                            200: '#b6dfdb',
                            300: '#7ac4be',
                            400: '#5eead4',
                            500: '#2dd4bf',
                            600: '#0d9488',
                            700: '#0b7970',
                            800: '#09655c',
                            900: '#065f46',
                            950: '#042f2e',
                        },
                        violet: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            200: '#b6dfdb',
                            300: '#7ac4be',
                            400: '#7ac4be',
                            500: '#5eead4',
                            600: '#0d9488',
                            700: '#0b7970',
                            800: '#09655c',
                            900: '#065f46',
                            950: '#042f2e',
                        },
                        purple: {
                            50: '#ecfdf5',
                            100: '#ccfbf1',
                            200: '#b6dfdb',
                            300: '#7ac4be',
                            400: '#7ac4be',
                            500: '#2dd4bf',
                            600: '#0d9488',
                            700: '#0b7970',
                            800: '#09655c',
                            900: '#065f46',
                            950: '#042f2e',
                        },
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

        :root, .panel-app, .admin-app {
            --theme-accent: #0d9488;
            --theme-accent-bright: #7ac4be;
            --theme-accent-light: #b6dfdb;
            --theme-accent-dark: #0b7970;
            --theme-accent-deeper: #09655c;
            --theme-accent-soft: rgba(13, 148, 136, 0.12);
            --theme-accent-soft-border: rgba(13, 148, 136, 0.28);
            --theme-accent-glow: rgba(13, 148, 136, 0.28);
            --theme-gradient: linear-gradient(125deg, #7ac4be 0%, #0d9488 48%, #0b7970 100%);
            --theme-gradient-btn: linear-gradient(115deg, #0b7970 0%, #0d9488 52%, #7ac4be 110%);
            --sb-accent-start: #0b7970;
            --sb-accent-mid: #0d9488;
            --sb-accent-end: #7ac4be;
            --sb-active-start: #0d9488;
            --sb-active-end: #7ac4be;
            --sb-icon: #0d9488;
            --sb-accent-line: #0d9488;
            --sb-rail-bottom: rgba(13, 148, 136, 0.35);
            --sb-hover-bg: rgba(13, 148, 136, 0.08);
            --sb-text: #334155;
            --sb-shell-bg: linear-gradient(180deg, #ffffff 0%, #ffffff 100%);
            --menu-bg: #ffffff;
            --menu-text: #334155;
            --menu-accent: #0d9488;
            --menu-accent-rgb: 13, 148, 136;
            --brand-gold: #0d9488;
            --brand-gold-bright: #7ac4be;
            --brand-gold-light: #b6dfdb;
            --brand-gold-dark: #0b7970;
            --brand-gold-dim: #09655c;
            --hero-gold: #0d9488;
            --topbar-border: rgba(13, 148, 136, 0.28);
        }

        body.panel-app {
            background: var(--panel-page-bg, #f3f4f6);
            color: var(--sb-text, #334155);
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
            background: var(--panel-sidebar-stripe, linear-gradient(180deg, #0d9488 0%, #8b5cf6 50%, #3b82f6 100%));
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
            scrollbar-color: var(--panel-sidebar-accent, #0d9488) transparent;
        }
        .panel-sidebar-nav::-webkit-scrollbar { width: 5px; }
        .panel-sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .panel-sidebar-nav::-webkit-scrollbar-thumb {
            background: var(--panel-sidebar-accent-gradient, linear-gradient(180deg, #0d9488, #8b5cf6));
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
            color: var(--panel-sidebar-accent-dark, #0b7970) !important;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.06);
        }
        .sidebar-link:hover:not(.active) .sidebar-icon-wrap {
            background: rgba(255, 255, 255, 0.55);
            color: var(--panel-sidebar-accent, #0d9488);
        }
        .sidebar-link.active {
            background: var(--panel-sidebar-active-bg, linear-gradient(90deg, rgba(99,102,241,0.22) 0%, rgba(139,92,246,0.14) 100%));
            border-left-color: var(--panel-sidebar-accent, #0d9488);
            color: var(--panel-sidebar-accent-dark, #0b7970) !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6), 0 4px 14px rgba(99, 102, 241, 0.12);
        }
        .sidebar-link.active .sidebar-icon-wrap {
            background: var(--panel-sidebar-accent-gradient, linear-gradient(135deg, #0d9488, #8b5cf6));
            color: #fff;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);
        }

        .panel-sidebar-group-btn:hover:not(.panel-sidebar-group-active) {
            background: var(--panel-sidebar-hover-bg);
            color: var(--panel-sidebar-accent-dark, #0b7970) !important;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.06);
        }
        .panel-sidebar-group-active {
            background: var(--panel-sidebar-active-bg);
            color: var(--panel-sidebar-accent-dark, #0b7970) !important;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.5), 0 2px 10px rgba(99, 102, 241, 0.1);
        }
        .panel-sidebar-group-active .sidebar-icon-wrap {
            background: var(--panel-sidebar-accent-gradient);
            color: #fff;
            box-shadow: 0 3px 10px rgba(99, 102, 241, 0.3);
        }
        .panel-sidebar-sub-active {
            background: var(--panel-sidebar-active-bg);
            color: var(--panel-sidebar-accent-dark, #0b7970) !important;
            font-weight: 600;
            box-shadow: inset 3px 0 0 var(--panel-sidebar-accent, #0d9488);
        }
        .panel-sidebar-sub-border {
            border-left-color: var(--panel-sidebar-accent, #7ac4be) !important;
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
            color: var(--panel-sidebar-accent, #0d9488);
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
            border-color: var(--theme-accent, #0d9488);
            box-shadow: 0 0 0 3px var(--theme-accent-soft, rgba(13, 148, 136, 0.12));
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
            border-color: var(--theme-accent, #0d9488);
            box-shadow: 0 0 0 3px var(--theme-accent-soft, rgba(13, 148, 136, 0.12));
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
            background: var(--theme-gradient-btn, linear-gradient(to right, #0b7970, #0d9488));
            box-shadow: 0 4px 14px var(--theme-accent-glow, rgba(13, 148, 136, 0.28));
            transition: all 0.15s;
        }
        .panel-btn-primary:hover {
            filter: brightness(1.05);
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
            background: var(--theme-accent-soft, rgba(13, 148, 136, 0.06));
        }

        .panel-table tbody td {
            color: #475569;
        }

        .panel-app table.panel-table td:has(.action-icon-btn),
        .panel-app table.panel-table th.col-actions,
        .panel-app table.panel-table td.col-actions {
            width: 1%;
            white-space: nowrap;
            text-align: right;
            padding-left: .75rem;
            padding-right: .75rem;
        }

        .panel-app table.panel-table th.col-narrow,
        .panel-app table.panel-table td.col-narrow {
            white-space: nowrap;
        }

        /* Pagination light theme */
        .panel-app nav[role="navigation"] span, .panel-app nav[role="navigation"] a {
            color: #64748b !important;
        }
        .panel-app input[type="checkbox"],
        .panel-app input[type="radio"] {
            accent-color: var(--theme-accent, #0d9488);
        }

        .panel-app .action-icon-btn--edit {
            color: var(--theme-accent, #0d9488) !important;
            border-color: var(--theme-accent-light, #b6dfdb) !important;
            background: var(--theme-accent-soft, rgba(13, 148, 136, 0.12)) !important;
        }
        .panel-app .action-icon-btn--edit:hover {
            background: rgba(13, 148, 136, 0.18) !important;
        }

        .panel-app nav[role="navigation"] a.bg-gray-800,
        .panel-app nav[role="navigation"] span.bg-gray-800 {
            background: var(--theme-accent, #0d9488) !important;
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
            padding-top: 8rem;
        }
        .panel-sidebar-horizontal-nav {
            scrollbar-width: thin;
            overflow: visible;
        }
        .panel-sidebar-horizontal-nav-scroll {
            overflow-x: auto;
            overflow-y: hidden;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .panel-sidebar-horizontal-nav-scroll::-webkit-scrollbar {
            display: none;
            height: 0;
            width: 0;
        }
        .panel-sidebar-horizontal-link {
            color: var(--panel-sidebar-text, #334155);
        }
        .panel-sidebar-horizontal-dropdown {
            position: fixed;
            z-index: 100;
            scrollbar-width: thin;
            scrollbar-color: var(--panel-sidebar-accent, #0d9488) transparent;
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
            background: var(--panel-sidebar-accent-gradient, linear-gradient(180deg, #0d9488 0%, #8b5cf6 100%));
            border-radius: 9999px;
            border: 1px solid rgba(255, 255, 255, 0.65);
            box-shadow: 0 1px 4px rgba(99, 102, 241, 0.25);
        }
        .panel-sidebar-horizontal-dropdown::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #0b7970 0%, #0d9488 100%);
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
        .panel-sidebar-skeleton-horizontal { height: 8rem; }

        /* ── NRI Suvidha flat sidebar (solid colors, Inter 14px) ── */
        body.panel-sidebar-flat .panel-sidebar {
            background-color: var(--panel-sidebar-bg, #ffffff) !important;
            background-image: none !important;
            border-right-color: var(--panel-sidebar-border, #e5e7eb);
            box-shadow: 1px 0 0 #e5e7eb;
        }
        body.panel-sidebar-flat .panel-sidebar::after,
        body.panel-sidebar-flat .panel-sidebar-glow-bottom { display: none !important; }
        body.panel-sidebar-flat .panel-sidebar::before {
            width: 3px;
            background: var(--panel-sidebar-stripe, #0d9488) !important;
        }
        body.panel-sidebar-flat .panel-sidebar-header,
        body.panel-sidebar-flat .panel-sidebar-footer {
            background: #ffffff !important;
            backdrop-filter: none;
            -webkit-backdrop-filter: none;
            border-color: var(--panel-sidebar-border, #e5e7eb);
        }
        body.panel-sidebar-flat .panel-sidebar-user {
            background: #f9fafb !important;
            border: 1px solid var(--panel-sidebar-border, #e5e7eb);
            box-shadow: none;
            backdrop-filter: none;
        }
        body.panel-sidebar-flat .panel-sidebar-nav,
        body.panel-sidebar-flat .panel-sidebar-horizontal-nav {
            font-family: 'Inter', system-ui, sans-serif;
            font-size: var(--panel-sidebar-nav-font-size, 0.875rem);
        }
        body.panel-sidebar-flat .sidebar-link,
        body.panel-sidebar-flat .panel-sidebar-group-btn,
        body.panel-sidebar-flat .panel-sidebar-sub-link {
            font-family: 'Inter', system-ui, sans-serif;
            font-size: var(--panel-sidebar-nav-font-size, 0.875rem);
            font-weight: 500;
            color: var(--panel-sidebar-text, #334155) !important;
        }
        body.panel-sidebar-flat .sidebar-link:hover:not(.active),
        body.panel-sidebar-flat .panel-sidebar-group-btn:hover:not(.panel-sidebar-group-active),
        body.panel-sidebar-flat .panel-sidebar-sub-link:hover:not(.panel-sidebar-sub-active) {
            background: var(--sb-hover-bg, var(--panel-sidebar-hover-bg)) !important;
            color: var(--theme-accent-dark, var(--panel-sidebar-accent-dark)) !important;
            box-shadow: none;
        }
        body.panel-sidebar-flat .sidebar-link.active,
        body.panel-sidebar-flat .panel-sidebar-group-active,
        body.panel-sidebar-flat .panel-sidebar-sub-active {
            background: var(--theme-accent-soft, var(--panel-sidebar-active-bg)) !important;
            color: var(--theme-accent-dark, var(--panel-sidebar-accent-dark)) !important;
            box-shadow: none;
        }
        body.panel-sidebar-flat .sidebar-link.active .sidebar-icon-wrap,
        body.panel-sidebar-flat .panel-sidebar-group-active .sidebar-icon-wrap {
            background: var(--theme-gradient, var(--panel-sidebar-accent-gradient)) !important;
            color: #fff !important;
            box-shadow: none;
        }
        body.panel-sidebar-flat .sidebar-link:hover:not(.active) .sidebar-icon-wrap {
            background: var(--theme-accent-soft, rgba(13, 148, 136, 0.12));
            color: var(--sb-icon, var(--menu-accent, #0d9488));
        }
        body.panel-sidebar-flat .sidebar-icon-wrap {
            color: var(--sb-icon, var(--menu-accent, #0d9488));
        }
        body.panel-sidebar-flat .panel-sidebar-brand-sub {
            color: var(--panel-sidebar-text-muted, #64748b);
            font-weight: 500;
        }
        body.panel-sidebar-flat .panel-sidebar-nav::-webkit-scrollbar-thumb {
            background: var(--panel-sidebar-accent, #0d9488);
        }

        /* Horizontal top bar — NRI Suvidha style (white header + light nav + teal active pill) */
        body.panel-sidebar-flat.panel-layout-horizontal .panel-sidebar-horizontal {
            background-color: #ffffff !important;
            background-image: none !important;
            border-bottom: none;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
        }
        body.panel-sidebar-flat.panel-layout-horizontal .panel-sidebar-horizontal-brand {
            background: var(--menu-bg, #ffffff);
            border-bottom: 1px solid var(--topbar-border, var(--panel-sidebar-border));
        }
        body.panel-sidebar-flat.panel-layout-horizontal .panel-sidebar-horizontal-nav {
            background: var(--menu-bg, var(--panel-sidebar-horizontal-bg, #ffffff));
            padding-top: 0.375rem;
            padding-bottom: 0.5rem;
            border-bottom: none;
        }
        body.panel-sidebar-flat.panel-layout-horizontal .panel-sidebar-horizontal-nav-scroll a,
        body.panel-sidebar-flat.panel-layout-horizontal .panel-sidebar-horizontal-nav-scroll button {
            font-family: 'Inter', system-ui, sans-serif !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            color: var(--menu-text, var(--panel-sidebar-horizontal-text, #334155)) !important;
            border-radius: 0.5rem;
        }
        body.panel-sidebar-flat.panel-layout-horizontal .panel-sidebar-horizontal-nav-scroll a:hover,
        body.panel-sidebar-flat.panel-layout-horizontal .panel-sidebar-horizontal-nav-scroll button:hover {
            background: var(--sb-hover-bg, var(--panel-sidebar-hover-bg)) !important;
            color: var(--theme-accent-dark, var(--panel-sidebar-accent-dark)) !important;
        }
        body.panel-sidebar-flat.panel-layout-horizontal .panel-sidebar-horizontal-nav-scroll .panel-nav-item:not(.panel-sidebar-group-active) .panel-nav-icon {
            color: var(--sb-icon, var(--menu-accent, #0d9488));
        }
        body.panel-sidebar-flat.panel-layout-horizontal .panel-sidebar-horizontal-nav-scroll .panel-sidebar-group-active .panel-nav-icon,
        body.panel-sidebar-flat.panel-layout-horizontal .panel-sidebar-horizontal-nav-scroll .panel-sidebar-group-active .panel-nav-chevron {
            color: #ffffff;
        }
        body.panel-sidebar-flat.panel-layout-horizontal .panel-sidebar-horizontal-dropdown a {
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 0.875rem;
            color: var(--panel-sidebar-text, #334155) !important;
        }
        body.panel-sidebar-flat.panel-layout-horizontal .panel-sidebar-horizontal-dropdown a.text-indigo-700 {
            color: var(--panel-sidebar-accent-dark, #0f766e) !important;
            background: var(--panel-sidebar-active-bg, #f0fdfa) !important;
        }
        body.panel-sidebar-flat .panel-sidebar-logo-mark {
            background: var(--theme-gradient, var(--panel-sidebar-accent-gradient)) !important;
            box-shadow: none;
        }

        body.panel-sidebar-flat.panel-app {
            background: var(--panel-page-bg, #f3f4f6) !important;
        }

        /* Horizontal nav — icon + label items */
        .panel-nav-item {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.875rem;
            border-radius: 0.5rem;
            font-weight: 500;
            white-space: nowrap;
            transition: background 0.15s, color 0.15s, box-shadow 0.15s;
        }
        .panel-nav-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.125rem;
            height: 1.125rem;
            flex-shrink: 0;
            opacity: 0.95;
        }
        .panel-nav-chevron {
            width: 0.875rem;
            height: 0.875rem;
            flex-shrink: 0;
            opacity: 0.85;
            margin-left: -0.125rem;
        }

        .panel-nav-scroll-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.75rem;
            height: 1.75rem;
            border-radius: 0.5rem;
            color: var(--theme-accent, #0d9488);
            background: transparent;
            border: none;
            transition: background 0.15s, opacity 0.15s, color 0.15s;
            flex-shrink: 0;
        }
        .panel-nav-scroll-btn:not(:disabled):hover {
            background: var(--sb-hover-bg, rgba(13, 148, 136, 0.08));
            color: var(--theme-accent-dark, #0b7970);
        }
        .panel-nav-scroll-btn:disabled {
            opacity: 0.3;
            cursor: default;
        }

        body.panel-sidebar-flat.panel-layout-horizontal .panel-sidebar-horizontal-nav-scroll .panel-nav-item:hover:not(.panel-sidebar-group-active) {
            background: var(--sb-hover-bg, var(--panel-sidebar-hover-bg)) !important;
            color: var(--theme-accent-dark, var(--panel-sidebar-accent-dark)) !important;
        }
        body.panel-sidebar-flat.panel-layout-horizontal .panel-sidebar-horizontal-nav-scroll .panel-sidebar-group-active.panel-nav-item {
            background: var(--theme-gradient, linear-gradient(125deg, var(--sb-active-start, #0d9488), var(--sb-active-end, #7ac4be))) !important;
            color: #ffffff !important;
            box-shadow: 0 1px 3px var(--theme-accent-glow, rgba(13, 148, 136, 0.28));
        }
        body.panel-sidebar-flat.panel-layout-horizontal .panel-sidebar-horizontal-nav-scroll .panel-sidebar-group-active .panel-nav-icon,
        body.panel-sidebar-flat.panel-layout-horizontal .panel-sidebar-horizontal-nav-scroll .panel-sidebar-group-active .panel-nav-chevron {
            opacity: 1;
        }

        body.panel-sidebar-flat .sidebar-link.active {
            border-left: 3px solid var(--sb-accent-line, var(--theme-accent, #0d9488));
            padding-left: calc(0.75rem - 3px);
        }

        .panel-topbar {
            border-bottom-color: var(--topbar-border, rgba(13, 148, 136, 0.28)) !important;
        }

        .panel-user-menu-trigger { cursor: pointer; }
        .panel-user-menu-trigger:hover { background: #f8fafc; }
        .panel-user-menu-dropdown { font-size: 0.875rem; }

        /* Thin teal scrollbars — page & panels */
        .panel-app {
            scrollbar-width: thin;
            scrollbar-color: rgba(13, 148, 136, 0.38) transparent;
        }
        .panel-app ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        .panel-app ::-webkit-scrollbar-track {
            background: transparent;
        }
        .panel-app ::-webkit-scrollbar-thumb {
            background: rgba(13, 148, 136, 0.32);
            border-radius: 9999px;
            border: 1px solid transparent;
            background-clip: padding-box;
        }
        .panel-app ::-webkit-scrollbar-thumb:hover {
            background: rgba(13, 148, 136, 0.52);
            border-radius: 9999px;
        }
        .panel-app ::-webkit-scrollbar-corner {
            background: transparent;
        }
        .panel-sidebar-horizontal-dropdown {
            scrollbar-color: rgba(13, 148, 136, 0.38) transparent;
        }
        .panel-sidebar-horizontal-dropdown::-webkit-scrollbar-thumb {
            background: rgba(13, 148, 136, 0.35);
            border-radius: 9999px;
            border: none;
            box-shadow: none;
        }
        .panel-sidebar-horizontal-dropdown::-webkit-scrollbar-thumb:hover {
            background: rgba(13, 148, 136, 0.55);
        }
        .panel-sidebar-nav {
            scrollbar-color: rgba(13, 148, 136, 0.38) transparent;
        }
        .panel-sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(13, 148, 136, 0.35);
            border-radius: 9999px;
        }
        .panel-sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: rgba(13, 148, 136, 0.55);
        }
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
    $panelRole = auth()->user()?->role?->slug ?? '';
    $isCompanyPanelRoute = auth()->check() && request()->is('company', 'company/*');
    // Horizontal nav is company-only; platform (/admin) and learner panels always use vertical sidebar.
    $canUseHorizontalNav = $isCompanyPanelRoute && in_array($panelRole, ['admin', 'sub-admin', 'counselor'], true);
    $panelSidebarHorizontal = $canUseHorizontalNav && ($panelSidebarSettings['layout'] ?? 'vertical') === 'horizontal';
    $panelSidebarFlat = \App\Services\SidebarSettingsService::isFlatTheme($panelSidebarSettings);
@endphp
<body class="h-full panel-app admin-app font-sans antialiased {{ $panelSidebarHorizontal ? 'panel-layout-horizontal' : 'panel-layout-vertical' }} {{ $panelSidebarFlat ? 'panel-sidebar-flat' : '' }}"
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
