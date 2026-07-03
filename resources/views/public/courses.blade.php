<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses — Learnyst</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Plus Jakarta Sans','sans-serif']},colors:{brand:{400:'#34d399',500:'#10b981',600:'#059669'},surface:{950:'#020617'}}}}}</script>
</head>
<body class="bg-surface-950 text-slate-200 font-sans antialiased min-h-screen">
    <nav class="border-b border-slate-800/80 bg-surface-950/90 backdrop-blur-xl sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-500 to-brand-600 flex items-center justify-center text-white font-bold">L</div>
                <span class="text-white font-bold text-lg">Learnyst</span>
            </a>
            <div class="flex items-center gap-6">
                <a href="{{ route('public.courses') }}" class="text-sm text-brand-400">Courses</a>
                <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl bg-brand-600 text-white text-sm font-medium">Sign In</a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl font-bold text-white mb-8">All Courses</h1>
        @if($courses->count())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($courses as $course)
            <a href="{{ route('public.course', $course) }}" class="rounded-2xl overflow-hidden bg-slate-900/60 border border-slate-800 hover:border-brand-500/30 transition">
                @if($course->thumbnail)
                    <img src="{{ Storage::url($course->thumbnail) }}" alt="" class="w-full h-44 object-cover">
                @else
                    <div class="w-full h-44 bg-slate-800 flex items-center justify-center text-3xl font-bold text-brand-400">{{ strtoupper(substr($course->title, 0, 2)) }}</div>
                @endif
                <div class="p-5">
                    @if($course->category)<span class="text-xs text-brand-400">{{ $course->category->name }}</span>@endif
                    <h3 class="text-lg font-semibold text-white mt-1">{{ $course->title }}</h3>
                    <p class="text-sm text-slate-500 mt-2 line-clamp-2">{{ Str::limit($course->description, 100) }}</p>
                    <p class="text-brand-400 font-semibold mt-3">{{ $course->is_free ? 'Free' : '₹'.number_format($course->price, 0) }}</p>
                </div>
            </a>
            @endforeach
        </div>
        <div class="mt-8">{{ $courses->links() }}</div>
        @else
        <p class="text-slate-500 text-center py-16">No courses available yet.</p>
        @endif
    </main>
</body>
</html>
