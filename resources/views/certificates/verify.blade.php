<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Certificate — Learnyst</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Plus Jakarta Sans','sans-serif']},colors:{brand:{400:'#34d399',500:'#10b981',600:'#059669'},surface:{950:'#020617'}}}}}</script>
</head>
<body class="min-h-screen bg-surface-950 flex items-center justify-center p-4 font-sans">
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-lg">
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 mb-6">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-brand-600 flex items-center justify-center text-white font-bold">L</div>
                <span class="text-white font-bold text-lg">Learnyst</span>
            </a>
            <h1 class="text-2xl font-bold text-white">Verify Certificate</h1>
            <p class="text-slate-400 mt-2 text-sm">Enter certificate number to verify authenticity</p>
        </div>

        <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-2xl p-8 shadow-2xl">
            <form method="GET" action="{{ route('certificates.verify') }}" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Certificate Number</label>
                    <input type="text" name="number" value="{{ request('number') }}" required placeholder="CERT-XXXXXXXX"
                           class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono focus:outline-none focus:ring-2 focus:ring-brand-500/50">
                </div>
                <button type="submit" class="w-full py-3 rounded-xl bg-brand-600 text-white font-semibold hover:bg-brand-500 transition">Verify</button>
            </form>

            @if(request()->filled('number'))
                @if($certificate)
                <div class="mt-6 p-4 rounded-xl bg-brand-500/10 border border-brand-500/30">
                    <div class="flex items-center gap-2 text-brand-400 mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="font-semibold">Valid Certificate</span>
                    </div>
                    <dl class="space-y-2 text-sm">
                        <div><dt class="text-slate-500">Certificate #</dt><dd class="text-white font-mono">{{ $certificate->certificate_number }}</dd></div>
                        <div><dt class="text-slate-500">Issued To</dt><dd class="text-white">{{ $certificate->user?->name }}</dd></div>
                        <div><dt class="text-slate-500">Course</dt><dd class="text-white">{{ $certificate->course?->title ?? '—' }}</dd></div>
                        <div><dt class="text-slate-500">Issued On</dt><dd class="text-white">{{ $certificate->issued_at?->format('M d, Y') }}</dd></div>
                    </dl>
                </div>
                @else
                <div class="mt-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm">
                    No certificate found with this number. Please check and try again.
                </div>
                @endif
            @endif
        </div>
    </div>
</body>
</html>
