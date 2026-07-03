@extends('layouts.app')

@section('title', 'Copy Mock-Test')
@section('page-title', 'Copy Mock-Test')
@section('breadcrumb', 'Utilities / Copy Product / Copy Mock-Test')

@section('content')
<div class="pb-28" x-data="copyMockTestForm(@js($mockTests))">
    <div class="space-y-8 max-w-3xl">
        <div>
            <a href="{{ route('admin.utilities.copy-product') }}" class="inline-flex items-center gap-1 text-sm text-slate-600 hover:text-slate-900 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Copy Product
            </a>
            <p class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase mt-3">
                Utilities <span class="mx-1">/</span> Copy Product <span class="mx-1">/</span>
                <span class="text-emerald-600">Copy Mock-Test</span>
            </p>
        </div>

        <div>
            <h3 class="text-2xl font-bold text-slate-900">Copy Mock-Test</h3>
            <p class="text-sm text-slate-500 mt-2">Select mock-test you want to copy</p>
        </div>

        <div class="glass-card rounded-2xl p-6">
            <label class="block text-sm font-bold text-slate-900 mb-3">Select Mock-Test</label>
            <div class="flex gap-3">
                <input type="text"
                       readonly
                       :value="selectedMockTest ? selectedMockTest.title : ''"
                       placeholder="Select a mock-test to copy"
                       class="panel-input flex-1 cursor-default bg-white">
                <button type="button"
                        @click="modalOpen = true; search = ''"
                        class="shrink-0 px-5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm font-medium text-slate-700 hover:bg-slate-100 transition">
                    Select
                </button>
            </div>
        </div>
    </div>

    <div x-show="modalOpen"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="modalOpen = false">
        <div class="absolute inset-0 bg-slate-900/40" @click="modalOpen = false"></div>
        <div class="relative w-full max-w-lg glass-card rounded-2xl shadow-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h4 class="text-base font-bold text-slate-900">Select Mock-Test</h4>
                <button type="button" @click="modalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4 border-b border-slate-100">
                <input type="text"
                       x-model="search"
                       placeholder="Search mock tests..."
                       class="panel-input">
            </div>
            <div class="max-h-80 overflow-y-auto">
                <template x-for="mockTest in filteredMockTests" :key="mockTest.id">
                    <button type="button"
                            @click="selectMockTest(mockTest)"
                            class="w-full text-left px-6 py-3.5 text-sm text-slate-700 hover:bg-emerald-50 border-b border-slate-50 last:border-0 transition"
                            :class="selectedMockTest?.id === mockTest.id ? 'bg-emerald-50 text-emerald-700 font-medium' : ''">
                        <span x-text="mockTest.title"></span>
                    </button>
                </template>
                <p x-show="filteredMockTests.length === 0" class="px-6 py-8 text-sm text-slate-500 text-center">No mock tests found.</p>
            </div>
        </div>
    </div>

    <div class="fixed bottom-0 left-0 right-0 lg:left-72 z-40 bg-white border-t border-slate-200 shadow-[0_-4px_24px_rgba(15,23,42,0.06)]">
        <div class="max-w-3xl px-4 sm:px-6 lg:px-8 py-4 flex items-center gap-3">
            <button type="button"
                    :disabled="!selectedMockTest"
                    :class="selectedMockTest
                        ? 'px-8 py-2.5 rounded-xl text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 transition'
                        : 'px-8 py-2.5 rounded-xl text-sm font-semibold text-white bg-slate-300 cursor-not-allowed'">
                Next
            </button>
            <a href="{{ route('admin.utilities.copy-product') }}"
               class="px-6 py-2.5 rounded-xl border border-slate-200 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                Cancel
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyMockTestForm(mockTests) {
    return {
        mockTests,
        selectedMockTest: null,
        modalOpen: false,
        search: '',

        get filteredMockTests() {
            const query = this.search.trim().toLowerCase();
            if (!query) {
                return this.mockTests;
            }
            return this.mockTests.filter(item => item.title.toLowerCase().includes(query));
        },

        selectMockTest(mockTest) {
            this.selectedMockTest = mockTest;
            this.modalOpen = false;
        },
    };
}
</script>
@endpush
