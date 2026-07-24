<div class="space-y-6">
    <div class="bg-gradient-to-r from-emerald-50 to-white border border-emerald-100 rounded-2xl p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h3 class="text-lg font-bold text-slate-900">Course Settings</h3>
            <p class="text-sm text-slate-500 mt-1">Manage branding, pricing, features, publishing, and more from the settings hub.</p>
        </div>
        <a href="{{ route('admin.courses.settings.hub', $course) }}"
           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition shadow-sm">
            Open Course Settings
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach([
            ['Branding', 'Course image, video & details', 'branding'],
            ['Pricing Plans', 'Free, one-time, offers & more', 'pricing-plans'],
            ['Certificates', 'Criteria & certificate templates', 'certificates'],
            ['Publish Course', 'Go live or unpublish', 'publish'],
            ['Permissions', 'Access & selling platforms', 'permissions'],
            ['Remove Learners', 'Revoke course access securely', 'remove-learners'],
        ] as [$title, $desc, $key])
            <a href="{{ route('admin.courses.settings.show', [$course, $key]) }}"
               class="bg-white border border-slate-200 rounded-2xl p-5 hover:border-emerald-300 hover:shadow-md transition">
                <h4 class="font-semibold text-slate-900">{{ $title }}</h4>
                <p class="text-sm text-slate-500 mt-1">{{ $desc }}</p>
            </a>
        @endforeach
    </div>

    <details class="bg-white border border-slate-200 rounded-2xl p-5">
        <summary class="cursor-pointer font-semibold text-slate-800">Quick edit (legacy form)</summary>
        <form method="POST" action="{{ route('admin.courses.update', $course) }}" enctype="multipart/form-data" class="space-y-6 mt-6">
            @csrf
            @method('PUT')
            @include('admin.courses._form', ['course' => $course])
            <div class="flex justify-end">
                <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary transition">Save Changes</button>
            </div>
        </form>
    </details>
</div>
