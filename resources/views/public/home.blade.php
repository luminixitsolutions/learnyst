<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learnyst — Premium Learning Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Plus Jakarta Sans','sans-serif']},colors:{brand:{400:'#34d399',500:'#10b981',600:'#059669'},surface:{950:'#020617'}}}}}</script>
</head>
<body class="bg-surface-950 text-slate-200 font-sans antialiased">
    <nav class="border-b border-slate-800/80 bg-surface-950/90 backdrop-blur-xl sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-500 to-brand-600 flex items-center justify-center text-white font-bold">L</div>
                <span class="text-white font-bold text-lg">Learnyst</span>
            </a>
            <div class="flex items-center gap-6">
                <a href="{{ route('public.courses') }}" class="text-sm text-slate-400 hover:text-white">Courses</a>
                <a href="{{ route('certificates.verify') }}" class="text-sm text-slate-400 hover:text-white">Verify Certificate</a>
                @auth
                    @php
                        $dash = match(auth()->user()->role?->slug) {
                            'admin', 'sub-admin' => route('admin.dashboard'),
                            'instructor' => route('instructor.dashboard'),
                            default => route('learner.dashboard'),
                        };
                    @endphp
                    <a href="{{ $dash }}" class="text-sm text-brand-400">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl bg-brand-600 text-white text-sm font-medium hover:bg-brand-500">Sign In</a>
                @endauth
            </div>
        </div>
    </nav>

    <section class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-brand-600/10 via-transparent to-teal-600/5"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 relative">
            <div class="max-w-3xl">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-tight">Learn Without Limits</h1>
                <p class="text-lg text-slate-400 mt-6">Premium courses, expert instructors, and a community that helps you grow. Start your learning journey today.</p>
                <div class="flex flex-wrap gap-4 mt-8">
                    <a href="{{ route('public.courses') }}" class="px-6 py-3 rounded-xl bg-brand-600 text-white font-semibold hover:bg-brand-500 transition shadow-lg shadow-brand-500/20">Browse Courses</a>
                    <a href="{{ route('login') }}" class="px-6 py-3 rounded-xl border border-slate-700 text-white font-medium hover:bg-slate-800 transition">Get Started</a>
                </div>
            </div>
        </div>
    </section>

    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 py-4"><div class="p-4 rounded-xl bg-brand-500/10 border border-brand-500/30 text-brand-400 text-sm">{{ session('success') }}</div></div>
    @endif

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-white">Featured Courses</h2>
            <a href="{{ route('public.courses') }}" class="text-sm text-brand-400 hover:text-brand-300">View all →</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($courses as $course)
            <a href="{{ route('public.course', $course) }}" class="rounded-2xl overflow-hidden bg-slate-900/60 border border-slate-800 hover:border-brand-500/30 transition group">
                @if($course->thumbnail)
                    <img src="{{ $course->thumbnailUrl() }}" alt="" class="w-full h-44 object-cover">
                @else
                    <div class="w-full h-44 bg-slate-800 flex items-center justify-center text-3xl font-bold text-brand-400">{{ strtoupper(substr($course->title, 0, 2)) }}</div>
                @endif
                <div class="p-5">
                    @if($course->category)<span class="text-xs text-brand-400">{{ $course->category->name }}</span>@endif
                    <h3 class="text-lg font-semibold text-white mt-1 group-hover:text-brand-400">{{ $course->title }}</h3>
                    <p class="text-brand-400 font-semibold mt-2">{{ $course->is_free ? 'Free' : '₹'.number_format($course->price, 0) }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="rounded-2xl bg-slate-900/60 border border-slate-800 p-8 lg:p-12">
            <h2 class="text-2xl font-bold text-white mb-2">Get in Touch</h2>
            <p class="text-slate-400 mb-6">Interested in a course? Leave your details and we'll reach out.</p>
            <form method="POST" action="{{ route('leads.capture') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @csrf
                <input type="text" name="name" required placeholder="Your name" class="px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 text-white focus:ring-2 focus:ring-brand-500/50 focus:outline-none">
                <input type="email" name="email" required placeholder="Email" class="px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 text-white focus:outline-none">
                <input type="tel" name="phone" placeholder="Phone" class="px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 text-white focus:outline-none">
                <button type="submit" class="px-6 py-3 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-500">Submit</button>
            </form>
        </div>
    </section>

    <footer class="border-t border-slate-800 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <p class="text-sm text-slate-500">© {{ date('Y') }} Learnyst. All rights reserved.</p>
            <div class="flex flex-wrap items-center gap-4">
                @foreach(['facebook' => 'Facebook', 'youtube' => 'YouTube', 'linkedin' => 'LinkedIn', 'instagram' => 'Instagram', 'telegram' => 'Telegram', 'whatsapp' => 'WhatsApp', 'website' => 'Website'] as $key => $label)
                    @if(!empty($social[$key]))
                        <a href="{{ $social[$key] }}" target="_blank" rel="noopener" class="text-sm text-slate-400 hover:text-brand-400 transition">{{ $label }}</a>
                    @endif
                @endforeach
            </div>
        </div>
    </footer>
</body>
</html>
