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

        .sidebar-link.active {
            background: linear-gradient(90deg, #eef2ff 0%, #f5f3ff 100%);
            border-left: 3px solid #6366f1;
            color: #4f46e5 !important;
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
    </style>
    @stack('styles')
</head>
<body class="h-full panel-app font-sans antialiased" x-data="{ sidebarOpen: false, deleteModal: false, deleteForm: null }">
    <div class="flex h-full min-h-screen">
        @include('layouts.partials.sidebar')

        <div class="flex-1 flex flex-col min-w-0 lg:ml-72">
            @include('layouts.partials.topbar')

            <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-auto">
                @include('layouts.partials.alerts')
                @yield('content')
            </main>
        </div>
    </div>

    @include('layouts.partials.delete-modal')
    @include('layouts.partials.toast')

    @stack('scripts')
</body>
</html>
