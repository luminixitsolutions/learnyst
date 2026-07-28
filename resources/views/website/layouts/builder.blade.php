<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <meta name="description" content="@yield('meta_description', '')">
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('styles')
</head>
<body class="antialiased text-slate-800">
    @yield('content')
</body>
</html>
