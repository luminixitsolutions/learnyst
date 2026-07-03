@extends('layouts.app')

@section('title', $community->name)
@section('page-title', $community->name)
@section('breadcrumb', 'Communities / Details')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <x-badge :type="$community->is_active ? 'success' : 'danger'">{{ $community->is_active ? 'Active' : 'Inactive' }}</x-badge>
            @if($community->requires_approval)<x-badge type="warning" class="ml-2">Approval Required</x-badge>@endif
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.communities.edit', $community) }}" class="px-4 py-2 rounded-xl bg-brand-600 text-white text-sm">Edit</a>
            <a href="{{ route('admin.communities.index') }}" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-300 text-sm">Back</a>
        </div>
    </div>

    @if($community->description)
    <div class="glass-card rounded-2xl p-6"><p class="text-sm text-slate-500">{{ $community->description }}</p></div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="glass-card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Add Member</h3>
            <form method="POST" action="{{ route('admin.communities.members.add', $community) }}" class="space-y-4">
                @csrf
                <x-form-input label="Learner" name="user_id" type="select" required>
                    <option value="">Select learner</option>
                    @foreach($learners as $learner)
                        <option value="{{ $learner->id }}">{{ $learner->name }}</option>
                    @endforeach
                </x-form-input>
                <button type="submit" class="w-full py-2 rounded-xl bg-brand-600 text-white text-sm hover:bg-brand-500">Add Member</button>
            </form>
            <div class="mt-6">
                <p class="text-xs text-slate-500 mb-2">{{ $community->members->count() }} members</p>
                @foreach($community->members->take(10) as $member)
                    <p class="text-sm text-slate-500 py-1">{{ $member->name }}</p>
                @endforeach
            </div>
        </div>

        <div class="lg:col-span-2 glass-card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Posts</h3>
            <form method="POST" action="{{ route('admin.communities.posts.store', $community) }}" class="mb-6 p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                @csrf
                <x-form-input label="Title" name="title" />
                <x-form-input label="Body" name="body" type="textarea" required />
                <button type="submit" class="px-4 py-2 rounded-lg bg-brand-600 text-white text-sm">Post</button>
            </form>
            <div class="space-y-4">
                @forelse($community->posts as $post)
                <div class="p-4 rounded-xl bg-slate-900/40 border border-slate-200">
                    <div class="flex items-start justify-between">
                        <div>
                            @if($post->title)<p class="font-semibold text-slate-800">{{ $post->title }}</p>@endif
                            <p class="text-xs text-slate-500 mt-1">{{ $post->user?->name }} · {{ $post->created_at->diffForHumans() }}</p>
                        </div>
                        <form method="POST" action="{{ route('admin.communities.posts.destroy', $post) }}">@csrf @method('DELETE')
                            <button type="button" @click="deleteForm = $el.closest('form'); deleteModal = true" class="text-xs text-red-400">Delete</button>
                        </form>
                    </div>
                    <p class="text-sm text-slate-500 mt-2">{{ $post->body }}</p>
                </div>
                @empty
                <p class="text-sm text-slate-500 text-center py-8">No posts yet</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
