<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-white min-h-screen">
    <div class="max-w-3xl mx-auto px-6 py-16">
        <h1 class="text-4xl font-bold tracking-tight">{{ $page->headline ?: $page->title }}</h1>
        <div class="mt-6 text-slate-300 leading-relaxed whitespace-pre-line">{{ $page->body }}</div>

        @if(session('success'))
            <div class="mt-6 rounded-xl bg-emerald-900/40 border border-emerald-700 px-4 py-3 text-emerald-200 text-sm">{{ session('success') }}</div>
        @endif

        <div class="mt-10 flex flex-wrap gap-3">
            @if($page->cta_url)
            <a href="{{ route('public.landing.cta', $page->slug) }}" class="px-6 py-3 rounded-xl bg-emerald-500 text-slate-950 font-semibold">{{ $page->cta_text ?: 'Get started' }}</a>
            @endif
        </div>

        <form method="POST" action="{{ route('public.landing.lead', $page->slug) }}" class="mt-12 space-y-4 rounded-2xl border border-slate-800 p-6 bg-slate-900/50">
            @csrf
            <h2 class="text-lg font-semibold">Request a callback</h2>
            <input name="name" required placeholder="Name" class="w-full rounded-xl bg-slate-800 border-slate-700 px-4 py-3">
            <input name="email" type="email" required placeholder="Email" class="w-full rounded-xl bg-slate-800 border-slate-700 px-4 py-3">
            <input name="phone" placeholder="Phone" class="w-full rounded-xl bg-slate-800 border-slate-700 px-4 py-3">
            <button class="px-5 py-3 rounded-xl bg-white text-slate-950 font-semibold">Submit</button>
        </form>
    </div>
</body>
</html>
