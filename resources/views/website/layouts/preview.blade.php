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
    <link rel="stylesheet" href="{{ asset('website/css/learnyst-home.css') }}?v=46" type="text/css" media="all" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body.ly-company-preview {
            margin: 0;
            background: #f8fafc;
        }
    </style>
    @stack('styles')
</head>
<body class="ly-company-preview gdlr-core-body kingster-body kingster-body-front kingster-full">
    @yield('content')

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
    @stack('scripts')
</body>
</html>
