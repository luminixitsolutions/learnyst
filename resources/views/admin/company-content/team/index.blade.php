@extends('layouts.app')
@section('title', 'Team')
@section('page-title', 'Team')
@section('breadcrumb', 'Website / Team')
@section('content')
<div class="space-y-6 max-w-5xl">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-xl font-bold text-slate-900">Team</h3>
            <p class="text-sm text-slate-500">People shown on your public institute profile.</p>
        </div>
        <a href="{{ route('website.companies.show', $company->slug) }}#team" target="_blank" class="panel-btn-secondary">Preview</a>
    </div>

    <form method="POST" action="{{ route('admin.company-page.team.store') }}" enctype="multipart/form-data" class="glass-card rounded-2xl p-6 space-y-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <x-form-input label="Name" name="name" required />
            <x-form-input label="Role" name="role" />
        </div>
        <x-form-input label="Bio" name="bio" type="textarea" />
        <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-slate-700">Photo</label>
            <input type="file" name="photo" accept="image/*" class="panel-input">
        </div>
        <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="is_published" value="1" checked class="rounded border-slate-300"> Published</label>
        <button class="panel-btn-primary" type="submit">Add member</button>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($items as $item)
            <div class="glass-card rounded-2xl p-5 flex gap-4">
                @if($item->photoUrl())
                    <img src="{{ $item->photoUrl() }}" class="h-16 w-16 rounded-full object-cover" alt="">
                @endif
                <div class="flex-1">
                    <div class="font-semibold text-slate-900">{{ $item->name }}</div>
                    <div class="text-sm text-emerald-600">{{ $item->role }}</div>
                    <p class="text-sm text-slate-500 mt-1">{{ $item->bio }}</p>
                    <form method="POST" action="{{ route('admin.company-page.team.destroy', $item) }}" class="mt-2" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')
                        <button class="text-red-600 text-sm">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full glass-card rounded-2xl p-8 text-center text-slate-500">No team members yet.</div>
        @endforelse
    </div>
    {{ $items->links() }}
</div>
@endsection
