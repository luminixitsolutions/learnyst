<div class="glass-card rounded-2xl p-5">
    <h4 class="font-semibold text-slate-800 mb-4">Attachments</h4>

    @forelse($lesson->attachments as $attachment)
    <div class="flex items-center justify-between py-2 px-3 rounded-lg bg-slate-50 mb-2">
        <div>
            <p class="text-sm font-medium text-slate-700">{{ $attachment->title }}</p>
            <p class="text-xs text-slate-400">{{ $attachment->file_type }} · {{ $attachment->download_allowed ? 'Download allowed' : 'No download' }}</p>
        </div>
        <form method="POST" action="{{ route('admin.attachments.destroy', $attachment) }}">@csrf @method('DELETE')
            <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-xs text-red-500">Remove</button>
        </form>
    </div>
    @empty
    <p class="text-xs text-slate-400 mb-4">No attachments yet</p>
    @endforelse

    <form method="POST" action="{{ route('admin.lessons.attachments.store', $lesson) }}" enctype="multipart/form-data" class="space-y-3 mt-4 pt-4 border-t border-slate-200">
        @csrf
        <x-form-input label="Attachment Title" name="title" required />
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Upload File</label>
            <input type="file" name="file" required class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-indigo-600 file:text-white">
        </div>
        <x-form-input label="File Type" name="file_type" placeholder="pdf, doc, zip..." />
        <label class="flex items-center gap-2">
            <input type="checkbox" name="download_allowed" value="1" checked class="rounded border-slate-300 text-indigo-600">
            <span class="text-xs text-slate-600">Download Allowed</span>
        </label>
        <x-form-input label="Status" name="status" type="select" value="active">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </x-form-input>
        <button type="submit" class="w-full px-3 py-2 rounded-xl bg-slate-800 text-white text-xs hover:bg-slate-700">Add Attachment</button>
    </form>
</div>
