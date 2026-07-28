<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — {{ $webinar->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-white min-h-screen">
    <div class="max-w-lg mx-auto px-6 py-16">
        <h1 class="text-3xl font-bold">{{ $webinar->title }}</h1>
        @if($webinar->starts_at)
            <p class="text-slate-400 mt-2">{{ $webinar->starts_at->format('M d, Y · h:i A') }}</p>
        @endif
        @if($webinar->description)
            <p class="mt-4 text-slate-300">{{ $webinar->description }}</p>
        @endif

        @if(session('success'))
            <div class="mt-6 rounded-xl bg-emerald-900/40 border border-emerald-700 px-4 py-3 text-emerald-200 text-sm">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('public.webinars.register', $webinar) }}" class="mt-8 space-y-4">
            @csrf
            <input name="name" required placeholder="Full name" class="w-full rounded-xl bg-slate-800 border-slate-700 px-4 py-3" value="{{ old('name') }}">
            <input name="email" type="email" required placeholder="Email" class="w-full rounded-xl bg-slate-800 border-slate-700 px-4 py-3" value="{{ old('email') }}">
            <input name="phone" placeholder="Phone" class="w-full rounded-xl bg-slate-800 border-slate-700 px-4 py-3" value="{{ old('phone') }}">
            <button class="w-full px-5 py-3 rounded-xl bg-emerald-500 text-slate-950 font-semibold">Register</button>
        </form>
    </div>
</body>
</html>
