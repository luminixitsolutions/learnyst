<div class="glass-card rounded-2xl p-6">
    <h3 class="text-lg font-bold text-slate-800 mb-6">Course Settings</h3>
    <form method="POST" action="{{ route('admin.courses.update', $course) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.courses._form', ['course' => $course])
        <div class="flex justify-end">
            <button type="submit" class="px-5 py-2.5 rounded-xl panel-btn-primary transition">Save Changes</button>
        </div>
    </form>
</div>
