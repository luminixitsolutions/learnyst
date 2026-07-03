<div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="fixed inset-0 bg-slate-900/30 backdrop-blur-sm" @click="deleteModal = false"></div>
    <div class="relative bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl border border-slate-200">
        <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </div>
        <h3 class="text-lg font-bold text-slate-800 mb-2">Confirm Delete</h3>
        <p class="text-slate-500 text-sm mb-6">This action cannot be undone. Are you sure you want to delete this item?</p>
        <div class="flex gap-3 justify-end">
            <button @click="deleteModal = false" class="px-4 py-2 rounded-xl text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 transition">Cancel</button>
            <button @click="if(deleteForm) deleteForm.submit()" class="px-4 py-2 rounded-xl text-sm font-medium bg-red-600 text-white hover:bg-red-500 shadow-sm transition">Delete</button>
        </div>
    </div>
</div>
