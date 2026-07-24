@extends('layouts.app')
@section('title', 'Testimonials')
@section('page-title', 'Testimonials')
@section('breadcrumb', 'Website / Testimonials')
@section('content')
<div class="space-y-6 max-w-5xl">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h3 class="text-xl font-bold text-slate-900">Testimonials</h3>
            <p class="text-sm text-slate-500">Featured quotes shown on your public institute page.</p>
        </div>
        <a href="{{ route('website.companies.show', $company->slug) }}#testimonials" target="_blank" class="panel-btn-secondary">Preview</a>
    </div>

    <form method="POST" action="{{ route('admin.company-page.testimonials.store') }}" enctype="multipart/form-data" class="glass-card rounded-2xl p-6 space-y-4">
        @csrf
        <h4 class="text-sm font-semibold text-slate-800">Add testimonial</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-form-input label="Author name" name="author_name" required />
            <x-form-input label="Author title" name="author_title" placeholder="Student / Parent" />
            <x-form-input label="Rating (1-5)" name="rating" type="number" :value="5" />
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-slate-700">Avatar</label>
                <input type="file" name="avatar" accept="image/*" class="panel-input">
            </div>
        </div>
        <x-form-input label="Testimonial" name="content" type="textarea" required />
        <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="is_published" value="1" checked class="rounded border-slate-300"> Published</label>
        <button class="panel-btn-primary" type="submit">Add testimonial</button>
    </form>

    <div class="glass-card rounded-2xl overflow-hidden">
        <table class="w-full text-sm panel-table">
            <thead><tr class="text-left"><th class="px-6 py-4">Author</th><th class="px-6 py-4">Rating</th><th class="px-6 py-4">Status</th><th class="px-6 py-4"></th></tr></thead>
            <tbody>
            @forelse($items as $item)
                <tr>
                    <td class="px-6 py-4">
                        <div class="font-medium text-slate-800">{{ $item->author_name }}</div>
                        <div class="text-xs text-slate-500">{{ Str::limit($item->content, 80) }}</div>
                    </td>
                    <td class="px-6 py-4">{{ $item->rating }}/5</td>
                    <td class="px-6 py-4"><x-badge :type="$item->is_published ? 'success' : 'warning'">{{ $item->is_published ? 'Published' : 'Hidden' }}</x-badge></td>
                    <td class="px-6 py-4 text-right">
                        <form method="POST" action="{{ route('admin.company-page.testimonials.destroy', $item) }}" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')
                            <button class="text-red-600 text-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-6 py-8 text-center text-slate-500">No testimonials yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $items->links() }}
</div>
@endsection
