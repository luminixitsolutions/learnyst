<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course->title }} — Learnyst</title>
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
            <a href="{{ route('public.courses') }}" class="text-sm text-slate-400 hover:text-white">← All Courses</a>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                @if($course->thumbnail)
                    <img src="{{ Storage::url($course->thumbnail) }}" alt="" class="w-full h-64 object-cover rounded-2xl">
                @endif
                <div>
                    @if($course->category)<span class="text-sm text-brand-400">{{ $course->category->name }}</span>@endif
                    <h1 class="text-3xl font-bold text-white mt-2">{{ $course->title }}</h1>
                    <p class="text-slate-400 mt-4 whitespace-pre-line">{{ $course->description }}</p>
                </div>

                <div class="rounded-2xl bg-slate-900/60 border border-slate-800 p-6">
                    <h2 class="text-xl font-semibold text-white mb-4">Curriculum</h2>
                    @forelse($course->sections as $section)
                        <div class="mb-4">
                            <h3 class="text-sm font-medium text-brand-400">{{ $section->title }}</h3>
                            <ul class="mt-2 space-y-2">
                                @foreach($section->lessons as $lesson)
                                <li class="flex items-center gap-2 text-sm text-slate-400">
                                    <span class="w-5 h-5 rounded bg-slate-800 flex items-center justify-center text-xs text-brand-400">{{ $loop->iteration }}</span>
                                    {{ $lesson->title }}
                                    @if($lesson->is_preview)<span class="text-xs text-amber-400">Preview</span>@endif
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Curriculum coming soon.</p>
                    @endforelse
                </div>
            </div>

            <div>
                <div class="rounded-2xl bg-slate-900/60 border border-slate-800 p-6 sticky top-24">
                    <p class="text-3xl font-bold text-brand-400">{{ $course->is_free ? 'Free' : '₹'.number_format($course->price, 0) }}</p>
                    <p class="text-sm text-slate-500 mt-1 capitalize">{{ $course->access_type }} access</p>
                    <a href="{{ route('login') }}" class="block w-full text-center mt-6 px-6 py-3 rounded-xl bg-brand-600 text-white font-semibold hover:bg-brand-500 transition">
                        Enroll Now
                    </a>
                    @if($course->instructors->count())
                    <div class="mt-6 pt-6 border-t border-slate-800">
                        <p class="text-xs text-slate-500 mb-2">Instructors</p>
                        @foreach($course->instructors as $instructor)
                            <p class="text-sm text-white">{{ $instructor->name }}</p>
                        @endforeach
                    </div>
                    @endif
                    <form method="POST" action="{{ route('leads.capture') }}" class="mt-6 pt-6 border-t border-slate-800 space-y-3">
                        @csrf
                        <input type="hidden" name="course_id" value="{{ $course->id }}">
                        <p class="text-xs text-slate-500">Have questions? Get in touch.</p>
                        <input type="text" name="name" required placeholder="Name" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-700 text-white text-sm">
                        <input type="email" name="email" required placeholder="Email" class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-700 text-white text-sm">
                        <button type="submit" class="w-full py-2 rounded-lg bg-slate-800 text-white text-sm hover:bg-slate-700">Contact Us</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
