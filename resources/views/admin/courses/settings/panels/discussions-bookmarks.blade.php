@php
    $discussion = $settings->discussion_config ?? [];
    $bookmark = $settings->bookmark_config ?? [];
@endphp

<div>
    <h1 class="text-2xl font-bold text-slate-900">{{ $meta['title'] }}</h1>
    <p class="text-sm text-slate-500 mt-1">{{ $meta['description'] }}</p>
</div>

<form method="POST" action="{{ route('admin.courses.settings.update', [$course, $panel]) }}" class="mt-8 space-y-8" @change="markDirty()" x-on:input="markDirty()">
    @csrf
    @method('PUT')

    <section class="space-y-4">
        <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
            <div>
                <p class="text-sm font-medium text-slate-800">Enable discussions</p>
                <p class="text-xs text-slate-500">Learners can post and reply in course discussions</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="discussion_enabled" value="1" class="sr-only peer" @checked(old('discussion_enabled', $settings->discussion_enabled))>
                <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-slate-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
            </label>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach([
                'public_discussions' => ['Public discussions', 'Visible to all enrolled learners', $discussion['public'] ?? true],
                'private_discussions' => ['Private discussions', 'Allow private threads', $discussion['private'] ?? false],
                'allow_replies' => ['Allow replies', 'Learners can reply to posts', $discussion['allow_replies'] ?? true],
                'allow_attachments' => ['Allow attachments', 'Learners can attach files', $discussion['allow_attachments'] ?? false],
                'instructor_announcements' => ['Instructor announcements', 'Instructors can post announcements', $discussion['instructor_announcements'] ?? true],
                'moderation' => ['Moderation', 'Hold posts for review', $discussion['moderation'] ?? false],
                'report_flag' => ['Report / flag', 'Learners can report posts', $discussion['report_flag'] ?? true],
                'notifications' => ['Notifications', 'Notify participants of activity', $discussion['notifications'] ?? true],
            ] as $field => [$title, $help, $checked])
                <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
                    <div>
                        <p class="text-sm font-medium text-slate-800">{{ $title }}</p>
                        <p class="text-xs text-slate-500">{{ $help }}</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="{{ $field }}" value="1" class="sr-only peer" @checked(old($field, $checked))>
                        <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-slate-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                    </label>
                </div>
            @endforeach
        </div>
    </section>

    <section class="space-y-4">
        <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
            <div>
                <p class="text-sm font-medium text-slate-800">Enable bookmarks</p>
                <p class="text-xs text-slate-500">Learners can bookmark lessons for later</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="bookmarks_enabled" value="1" class="sr-only peer" @checked(old('bookmarks_enabled', $settings->bookmarks_enabled ?? true))>
                <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-slate-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
            </label>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
                <div>
                    <p class="text-sm font-medium text-slate-800">Allow bookmark notes</p>
                    <p class="text-xs text-slate-500">Learners can attach notes to bookmarks</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="allow_bookmark_notes" value="1" class="sr-only peer" @checked(old('allow_bookmark_notes', $bookmark['allow_notes'] ?? true))>
                    <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-slate-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                </label>
            </div>
            <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
                <div>
                    <p class="text-sm font-medium text-slate-800">Sync with progress</p>
                    <p class="text-xs text-slate-500">Keep bookmarks aligned with resume position</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="sync_bookmark_progress" value="1" class="sr-only peer" @checked(old('sync_bookmark_progress', $bookmark['sync_progress'] ?? true))>
                    <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-slate-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                </label>
            </div>
        </div>
    </section>

    <div class="sticky bottom-0 -mx-6 md:-mx-8 mt-10 px-6 md:px-8 py-4 border-t border-slate-200 bg-white flex items-center justify-end gap-3 rounded-b-2xl">
        <a href="{{ route('admin.courses.settings.hub', $course) }}"
           @click.prevent="requestLeave('{{ route('admin.courses.settings.hub', $course) }}')"
           class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Cancel</a>
        <button type="submit" :disabled="!dirty"
                class="px-5 py-2.5 rounded-xl text-sm font-semibold transition disabled:cursor-not-allowed"
                :class="dirty ? 'bg-slate-900 text-white hover:bg-slate-800' : 'bg-slate-100 text-slate-400'">Save</button>
    </div>
</form>
