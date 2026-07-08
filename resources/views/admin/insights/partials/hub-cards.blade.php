@props(['cards', 'backRoute' => null])

<div class="space-y-6">
    @if($backRoute)
    <div class="flex justify-end">
        <a href="{{ $backRoute }}" class="text-sm text-slate-500 hover:text-indigo-600">← Back</a>
    </div>
    @endif
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($cards as [$title, $desc, $route])
        <a href="{{ route($route) }}" class="glass-card rounded-2xl p-6 hover:border-indigo-400/30 transition group">
            <h3 class="text-lg font-bold text-slate-800 group-hover:text-indigo-600">{{ $title }}</h3>
            @if($desc)<p class="text-sm text-slate-500 mt-2">{{ $desc }}</p>@endif
            <span class="inline-block mt-4 text-sm text-indigo-600">View insight →</span>
        </a>
        @endforeach
    </div>
</div>
