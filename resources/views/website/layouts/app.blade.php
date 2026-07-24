<!DOCTYPE html>
<html lang="en-US" class="no-js">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('website.brand') . ' – ' . config('website.tagline'))</title>
    <meta name="description" content="@yield('meta_description', 'All-in-one platform to create, market and sell courses, mock tests & live classes securely from your own website and mobile apps.')">

    <link rel="stylesheet" href="{{ asset('website/plugins/goodlayers-core/plugins/combine/style.css') }}" type="text/css" media="all" />
    <link rel="stylesheet" href="{{ asset('website/plugins/goodlayers-core/include/css/page-builder.css') }}" type="text/css" media="all" />
    <link rel="stylesheet" href="{{ asset('website/css/style-core.css') }}" type="text/css" media="all" />
    <link rel="stylesheet" href="{{ asset('website/css/kingster-style-custom.css') }}" type="text/css" media="all" />
    <link rel="stylesheet" href="{{ asset('website/css/learnyst-home.css') }}?v=29" type="text/css" media="all" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @stack('styles')
</head>
<body class="home page-template-default page gdlr-core-body woocommerce-no-js tribe-no-js kingster-body kingster-body-front kingster-full kingster-with-sticky-navigation kingster-blockquote-style-1 gdlr-core-link-to-lightbox">
    @include('website.partials.header')

    <div class="kingster-body-outer-wrapper">
        <div class="kingster-body-wrapper clearfix kingster-with-frame">
            @include('website.partials.topbar')
            @include('website.partials.nav')

            @yield('content')

            @include('website.partials.footer')
        </div>
    </div>

    <script type="text/javascript" src="{{ asset('website/js/jquery/jquery.js') }}"></script>
    <script type="text/javascript" src="{{ asset('website/js/jquery/jquery-migrate.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('website/plugins/goodlayers-core/plugins/combine/script.js') }}"></script>
    <script type="text/javascript">
        var gdlr_core_pbf = { "admin": "", "video": { "width": "640", "height": "360" }, "ajax_url": "#" };
    </script>
    <script type="text/javascript" src="{{ asset('website/plugins/goodlayers-core/include/js/page-builder.js') }}"></script>
    <script type="text/javascript" src="{{ asset('website/js/jquery/ui/effect.min.js') }}"></script>
    <script type="text/javascript">
        var kingster_script_core = { "home_url": "{{ url('/') }}" };
    </script>
    <script type="text/javascript" src="{{ asset('website/js/plugins.min.js') }}"></script>
    <script>
    (function () {
        var menus = document.querySelectorAll('[data-ly-user-menu]');
        if (!menus.length) return;

        function closeAll(except) {
            menus.forEach(function (menu) {
                if (except && menu === except) return;
                menu.classList.remove('is-open');
                var toggle = menu.querySelector('[data-ly-user-toggle]');
                var dropdown = menu.querySelector('[data-ly-user-dropdown]');
                if (toggle) toggle.setAttribute('aria-expanded', 'false');
                if (dropdown) dropdown.hidden = true;
            });
        }

        menus.forEach(function (menu) {
            var toggle = menu.querySelector('[data-ly-user-toggle]');
            var dropdown = menu.querySelector('[data-ly-user-dropdown]');
            if (!toggle || !dropdown) return;

            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var willOpen = dropdown.hidden;
                closeAll();
                if (willOpen) {
                    menu.classList.add('is-open');
                    dropdown.hidden = false;
                    toggle.setAttribute('aria-expanded', 'true');
                }
            });
        });

        document.addEventListener('click', function () { closeAll(); });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeAll();
        });
    })();
    </script>
    @stack('scripts')
</body>
</html>
