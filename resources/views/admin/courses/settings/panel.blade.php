@extends('layouts.app')

@section('title', $meta['title'].' — Course Settings')
@section('page-title', $meta['title'])
@section('breadcrumb', $course->title)

@section('content')
<div class="min-h-[70vh]" x-data="courseSettingsPanel()">
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('admin.courses.settings.hub', $course) }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <aside class="lg:col-span-3">
            <div class="bg-white border border-slate-200 rounded-2xl p-4 sticky top-24">
                <h2 class="text-sm font-bold text-slate-800 px-2 mb-3">{{ $group['label'] }}</h2>
                <nav class="space-y-1">
                    @foreach($groupPanels as $key => $item)
                        <a href="{{ route('admin.courses.settings.show', [$course, $key]) }}"
                           class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm transition {{ $panel === $key ? 'bg-emerald-50 text-emerald-700 font-semibold border-l-4 border-emerald-500' : 'text-slate-600 hover:bg-slate-50' }}">
                            {{ $item['title'] }}
                        </a>
                    @endforeach
                </nav>
            </div>
        </aside>

        <div class="lg:col-span-9">
            <div class="bg-white border border-slate-200 rounded-2xl min-h-[520px] flex flex-col">
                <div class="p-6 md:p-8 flex-1">
                    @include('admin.courses.settings.panels.'.$panel)
                </div>
            </div>
        </div>
    </div>

    {{-- Unsaved changes modal --}}
    <div x-show="confirmLeave" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
            <h3 class="text-lg font-bold text-slate-900 mb-2">Unsaved changes</h3>
            <p class="text-sm text-slate-500 mb-6">You have unsaved changes. Leave without saving?</p>
            <div class="flex justify-end gap-3">
                <button type="button" @click="confirmLeave=false" class="px-4 py-2 rounded-xl border border-slate-200 text-sm">Keep Editing</button>
                <button type="button" @click="forceLeave()" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm">Leave</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function courseSettingsPanel() {
    return {
        dirty: false,
        confirmLeave: false,
        leaveUrl: null,
        markDirty() { this.dirty = true; },
        markClean() { this.dirty = false; },
        requestLeave(url) {
            if (!this.dirty) { window.location = url; return; }
            this.leaveUrl = url;
            this.confirmLeave = true;
        },
        forceLeave() {
            this.dirty = false;
            if (this.leaveUrl) window.location = this.leaveUrl;
        }
    }
}
</script>
@endpush
