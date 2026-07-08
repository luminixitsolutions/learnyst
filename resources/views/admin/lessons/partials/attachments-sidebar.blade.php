<div class="border border-slate-200 rounded-xl bg-white p-5 min-h-[400px] flex flex-col" x-data="{ showForm: false }">
    <p class="text-sm text-slate-600 mb-4">Add attachments like docs, pdf or links</p>

    @if($lesson->attachments->count())
    <div class="space-y-2 mb-4 flex-1">
        @foreach($lesson->attachments as $attachment)
        <div class="flex items-center justify-between py-2 px-3 rounded-lg bg-slate-50 text-sm">
            <div class="min-w-0">
                <p class="font-medium text-slate-700 truncate">{{ $attachment->title }}</p>
                <p class="text-xs text-slate-400">{{ $attachment->file_type }}</p>
            </div>
            <form method="POST" action="{{ route('admin.attachments.destroy', $attachment) }}">@csrf @method('DELETE')
                <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-red-400 hover:text-red-600 text-xs">×</button>
            </form>
        </div>
        @endforeach
    </div>
    @else
    <div class="flex-1 flex flex-col items-center justify-center text-center py-8">
        <svg class="w-16 h-16 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
        <p class="text-sm text-slate-400">Add Attachments</p>
    </div>
    @endif

    <div class="flex items-center gap-2 mt-auto pt-4 border-t border-slate-100">
        <button type="button" @click="showForm = !showForm" class="flex-1 px-3 py-2 rounded-lg border border-slate-200 text-xs text-slate-600 hover:bg-slate-50">+ Add link</button>
        <button type="button" @click="showForm = true" class="flex-1 px-3 py-2 rounded-lg border border-slate-200 text-xs text-slate-600 hover:bg-slate-50 flex items-center justify-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
            Browse
        </button>
    </div>

    <div x-show="showForm" x-cloak class="mt-3">
        <form method="POST" action="{{ route('admin.lessons.attachments.store', $lesson) }}" enctype="multipart/form-data" class="space-y-2">
            @csrf
            <input type="text" name="title" placeholder="Attachment title" required class="w-full px-3 py-2 rounded-lg border border-slate-200 text-xs">
            <input type="file" name="file" required class="w-full text-xs text-slate-500">
            <input type="hidden" name="status" value="active">
            <input type="hidden" name="download_allowed" value="1">
            <button type="submit" class="w-full px-3 py-2 rounded-lg bg-slate-800 text-white text-xs">Upload</button>
        </form>
    </div>
</div>
