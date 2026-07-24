<div>
    <h1 class="text-2xl font-bold text-slate-900">{{ $meta['title'] }}</h1>
    <p class="text-sm text-slate-500 mt-1">{{ $meta['description'] }}</p>
</div>

<div class="mt-8 space-y-8">
    @if($faqs->isEmpty())
        <div class="rounded-xl border border-dashed border-slate-200 p-10 text-center">
            <p class="text-sm font-medium text-slate-700">No FAQs yet</p>
            <p class="text-sm text-slate-500 mt-1">Add frequently asked questions to help learners before they enroll.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($faqs as $faq)
                <div class="rounded-xl border border-slate-200 p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-900">{{ $faq->question }}</p>
                            <p class="text-sm text-slate-600 mt-2 whitespace-pre-line">{{ $faq->answer }}</p>
                        </div>
                        <form method="POST" action="{{ route('admin.courses.settings.faqs.destroy', [$course, $faq]) }}"
                              onsubmit="return confirm('Remove this FAQ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-medium text-rose-600 hover:text-rose-700 px-2 py-1">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="rounded-xl border border-slate-200 p-5">
        <h2 class="text-sm font-bold text-slate-900 mb-4">Add FAQ</h2>
        <form method="POST" action="{{ route('admin.courses.settings.faqs.store', $course) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Question</label>
                <input type="text" name="question" value="{{ old('question') }}" required maxlength="500"
                       class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">
                @error('question')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Answer</label>
                <textarea name="answer" rows="4" required
                          class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400">{{ old('answer') }}</textarea>
                @error('answer')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">Add FAQ</button>
            </div>
        </form>
    </div>

    <div class="flex justify-end pt-2">
        <a href="{{ route('admin.courses.settings.hub', $course) }}"
           class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Back to settings</a>
    </div>
</div>
