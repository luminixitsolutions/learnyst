@props(['title', 'description' => null, 'action' => null, 'actionLabel' => 'Create'])

<div class="flex flex-col items-center justify-center py-16 px-6 text-center">
    <div class="w-16 h-16 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center mb-4">
        <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
    </div>
    <h3 class="text-lg font-bold text-slate-800">{{ $title }}</h3>
    @if($description)<p class="text-sm text-slate-500 mt-2 max-w-sm">{{ $description }}</p>@endif
    @if($action)
        <a href="{{ $action }}" class="mt-6 panel-btn-primary">{{ $actionLabel }}</a>
    @endif
</div>
