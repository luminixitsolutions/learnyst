@extends('layouts.app')

@section('title', 'Sidebar Settings')
@section('page-title', 'Sidebar Settings')
@section('breadcrumb', 'Settings / Sidebar Settings')

@push('styles')
<style>
    .sidebar-theme-option input:checked + .sidebar-theme-card,
    .sidebar-theme-option:has(input:checked) .sidebar-theme-card {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.18);
    }
    .sidebar-layout-option input:checked + .sidebar-layout-card {
        border-color: #6366f1;
        background: linear-gradient(135deg, #eef2ff, #f5f3ff);
    }
    .menu-sort-item {
        cursor: grab;
        user-select: none;
    }
    .menu-sort-item:active { cursor: grabbing; }
    .menu-sort-ghost {
        opacity: 0.45;
        background: #eef2ff !important;
    }
</style>
@endpush

@section('content')
@php
    $customColors = $settings['custom_colors'] ?? \App\Services\SidebarSettingsService::defaultCustomColors();
@endphp
<div class="max-w-4xl space-y-6"
     x-data="{
         layout: @js(old('layout', $settings['layout'])),
         theme: @js(old('theme', $settings['theme'])),
         custom: {
             primary: @js(old('custom_primary', $customColors['primary'])),
             secondary: @js(old('custom_secondary', $customColors['secondary'])),
             bg_start: @js(old('custom_bg_start', $customColors['bg_start'])),
             bg_end: @js(old('custom_bg_end', $customColors['bg_end'])),
         },
         customPreview() {
             return `linear-gradient(135deg, ${this.custom.bg_start}, ${this.custom.secondary}, ${this.custom.bg_end})`;
         },
         syncColor(field, value) {
             if (/^#[0-9A-Fa-f]{6}$/.test(value)) {
                 this.custom[field] = value.toLowerCase();
             }
         }
     }">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <p class="text-sm text-slate-500">Customize sidebar layout, color theme, and menu order for your institute panel.</p>
        <form method="POST" action="{{ route('admin.settings.sidebar.reset') }}" onsubmit="return confirm('Reset sidebar settings to defaults?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="panel-btn-secondary text-sm">Reset to defaults</button>
        </form>
    </div>

    <form method="POST" action="{{ route('admin.settings.sidebar.update') }}" id="sidebar-settings-form" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="glass-card rounded-2xl p-6 sm:p-8 space-y-6">
            <h3 class="text-lg font-bold text-slate-800">Sidebar Layout</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <label class="sidebar-layout-option cursor-pointer">
                    <input type="radio" name="layout" value="vertical" class="sr-only" x-model="layout" @checked(old('layout', $settings['layout']) === 'vertical')>
                    <div class="sidebar-layout-card rounded-xl border-2 border-slate-200 p-5 transition hover:border-indigo-200">
                        <div class="flex gap-3">
                            <div class="w-14 h-20 rounded-lg bg-gradient-to-b from-indigo-100 to-violet-50 border border-indigo-100 shrink-0"></div>
                            <div class="flex-1 space-y-2 pt-1">
                                <div class="h-2.5 w-full rounded bg-slate-100"></div>
                                <div class="h-2.5 w-4/5 rounded bg-slate-100"></div>
                                <div class="h-2.5 w-3/5 rounded bg-slate-100"></div>
                            </div>
                        </div>
                        <p class="mt-4 font-semibold text-slate-800">Vertical Sidebar</p>
                        <p class="text-xs text-slate-500 mt-1">Classic left navigation — best for many menu groups.</p>
                    </div>
                </label>
                <label class="sidebar-layout-option cursor-pointer">
                    <input type="radio" name="layout" value="horizontal" class="sr-only" x-model="layout" @checked(old('layout', $settings['layout']) === 'horizontal')>
                    <div class="sidebar-layout-card rounded-xl border-2 border-slate-200 p-5 transition hover:border-indigo-200">
                        <div class="space-y-2">
                            <div class="h-8 w-full rounded-lg bg-gradient-to-r from-indigo-100 to-violet-50 border border-indigo-100"></div>
                            <div class="h-2.5 w-full rounded bg-slate-100"></div>
                            <div class="h-2.5 w-4/5 rounded bg-slate-100"></div>
                        </div>
                        <p class="mt-4 font-semibold text-slate-800">Horizontal Top Bar</p>
                        <p class="text-xs text-slate-500 mt-1">Menu across the top — more content width on wide screens.</p>
                    </div>
                </label>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 sm:p-8 space-y-6">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Sidebar Color Theme</h3>
                <p class="text-sm text-slate-500 mt-1">Pick a preset or create your own accent and background combination.</p>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach($themes as $key => $theme)
                <label class="sidebar-theme-option cursor-pointer">
                    <input type="radio" name="theme" value="{{ $key }}" class="sr-only" x-model="theme" @checked(old('theme', $settings['theme']) === $key)>
                    <div class="sidebar-theme-card rounded-xl border-2 border-slate-200 p-3 transition hover:border-indigo-200">
                        <div class="h-12 rounded-lg mb-2" style="background: {{ $theme['preview'] }}"></div>
                        <p class="text-sm font-semibold text-slate-800">{{ $theme['label'] }}</p>
                        <div class="flex items-center gap-1.5 mt-1.5">
                            <span class="w-4 h-4 rounded-full border border-white shadow-sm" style="background: {{ $theme['accent'] }}"></span>
                            <span class="text-[10px] text-slate-500 uppercase tracking-wide">{{ $key }}</span>
                        </div>
                    </div>
                </label>
                @endforeach

                <label class="sidebar-theme-option cursor-pointer sm:col-span-1">
                    <input type="radio" name="theme" value="custom" class="sr-only" x-model="theme" @checked(old('theme', $settings['theme']) === 'custom')>
                    <div class="sidebar-theme-card rounded-xl border-2 border-slate-200 p-3 transition hover:border-indigo-200 h-full">
                        <div class="h-12 rounded-lg mb-2 border border-white/70 shadow-inner transition-all duration-200"
                             :style="{ background: customPreview() }"></div>
                        <p class="text-sm font-semibold text-slate-800">Custom Combination</p>
                        <div class="flex items-center gap-1.5 mt-1.5">
                            <span class="w-4 h-4 rounded-full border border-white shadow-sm transition-colors duration-200"
                                  :style="{ background: custom.primary }"></span>
                            <span class="w-4 h-4 rounded-full border border-white shadow-sm transition-colors duration-200"
                                  :style="{ background: custom.secondary }"></span>
                            <span class="text-[10px] text-slate-500 uppercase tracking-wide">custom</span>
                        </div>
                    </div>
                </label>
            </div>

            <div x-show="theme === 'custom'"
                 x-cloak
                 x-transition
                 class="rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50/70 via-white to-violet-50/60 p-5 sm:p-6 space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h4 class="text-sm font-bold text-slate-800">Customize Color Combination</h4>
                        <p class="text-xs text-slate-500 mt-1">Adjust accent and background colors. Preview updates instantly.</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="w-8 h-8 rounded-full border-2 border-white shadow-md transition-colors duration-200"
                              :style="{ background: custom.primary }"></span>
                        <span class="w-8 h-8 rounded-full border-2 border-white shadow-md transition-colors duration-200"
                              :style="{ background: custom.secondary }"></span>
                        <span class="w-8 h-8 rounded-lg border-2 border-white shadow-md transition-colors duration-200"
                              :style="{ background: custom.bg_start }"></span>
                        <span class="w-8 h-8 rounded-lg border-2 border-white shadow-md transition-colors duration-200"
                              :style="{ background: custom.bg_end }"></span>
                    </div>
                </div>

                <div class="h-16 rounded-xl border border-white/80 shadow-inner transition-all duration-200"
                     :style="{ background: customPreview() }"></div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-2 uppercase tracking-wide">Primary Accent</label>
                        <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2.5">
                            <input type="color" x-model="custom.primary" class="w-10 h-10 rounded-lg border-0 cursor-pointer bg-transparent p-0">
                            <input type="text"
                                   name="custom_primary"
                                   x-model="custom.primary"
                                   @input="syncColor('primary', $event.target.value)"
                                   pattern="^#[0-9A-Fa-f]{6}$"
                                   class="flex-1 panel-input !py-2 font-mono text-sm"
                                   placeholder="#6366f1">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-2 uppercase tracking-wide">Secondary Accent</label>
                        <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2.5">
                            <input type="color" x-model="custom.secondary" class="w-10 h-10 rounded-lg border-0 cursor-pointer bg-transparent p-0">
                            <input type="text"
                                   name="custom_secondary"
                                   x-model="custom.secondary"
                                   @input="syncColor('secondary', $event.target.value)"
                                   pattern="^#[0-9A-Fa-f]{6}$"
                                   class="flex-1 panel-input !py-2 font-mono text-sm"
                                   placeholder="#8b5cf6">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-2 uppercase tracking-wide">Background Start</label>
                        <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2.5">
                            <input type="color" x-model="custom.bg_start" class="w-10 h-10 rounded-lg border-0 cursor-pointer bg-transparent p-0">
                            <input type="text"
                                   name="custom_bg_start"
                                   x-model="custom.bg_start"
                                   @input="syncColor('bg_start', $event.target.value)"
                                   pattern="^#[0-9A-Fa-f]{6}$"
                                   class="flex-1 panel-input !py-2 font-mono text-sm"
                                   placeholder="#dbeafe">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-2 uppercase tracking-wide">Background End</label>
                        <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2.5">
                            <input type="color" x-model="custom.bg_end" class="w-10 h-10 rounded-lg border-0 cursor-pointer bg-transparent p-0">
                            <input type="text"
                                   name="custom_bg_end"
                                   x-model="custom.bg_end"
                                   @input="syncColor('bg_end', $event.target.value)"
                                   pattern="^#[0-9A-Fa-f]{6}$"
                                   class="flex-1 panel-input !py-2 font-mono text-sm"
                                   placeholder="#ede9fe">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 sm:p-8 space-y-4">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Menu Order</h3>
                <p class="text-sm text-slate-500 mt-1">Drag items to reorder how menu groups appear in the sidebar.</p>
            </div>
            <ul id="menuSortList" class="space-y-2">
                @foreach($menuItems as $item)
                <li class="menu-sort-item flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm" data-key="{{ $item['key'] }}">
                    <input type="hidden" name="menu_order[]" value="{{ $item['key'] }}">
                    <svg class="drag-handle w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800">{{ $item['label'] }}</p>
                        <p class="text-xs text-slate-500">{{ $item['type'] === 'group' ? 'Menu group' : 'Direct link' }}</p>
                    </div>
                    <span class="text-[10px] font-medium uppercase tracking-wide text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full">{{ $item['type'] }}</span>
                </li>
                @endforeach
            </ul>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="panel-btn-primary">Save Sidebar Settings</button>
            <a href="{{ route('admin.dashboard') }}" class="panel-btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
(function () {
    const list = document.getElementById('menuSortList');
    if (!list || !window.Sortable) return;

    Sortable.create(list, {
        animation: 150,
        handle: '.drag-handle',
        ghostClass: 'menu-sort-ghost',
    });
})();
</script>
@endpush
