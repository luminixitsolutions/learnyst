<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\CommunityPost;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    public function index(Request $request)
    {
        $communities = Community::withCount('members', 'posts')->latest()->paginate(15);

        return view('admin.communities.index', compact('communities'));
    }

    public function create()
    {
        return view('admin.communities.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'requires_approval' => ['boolean'],
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_active'] = true;
        $validated['requires_approval'] = $request->boolean('requires_approval');

        $community = Community::create($validated);
        ActivityLogger::log('community_created', "Community {$community->name} created", $community);

        return redirect()->route('admin.communities.show', $community)->with('success', 'Community created.');
    }

    public function show(Community $community)
    {
        $community->load(['members', 'posts.user']);
        $learners = User::whereHas('role', fn ($q) => $q->where('slug', 'learner'))->orderBy('name')->get();

        return view('admin.communities.show', compact('community', 'learners'));
    }

    public function edit(Community $community)
    {
        return view('admin.communities.edit', compact('community'));
    }

    public function update(Request $request, Community $community)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'requires_approval' => ['boolean'],
        ]);

        $community->update($validated);

        return redirect()->route('admin.communities.show', $community)->with('success', 'Community updated.');
    }

    public function destroy(Community $community)
    {
        $community->delete();

        return redirect()->route('admin.communities.index')->with('success', 'Community deleted.');
    }

    public function addMember(Request $request, Community $community)
    {
        $validated = $request->validate(['user_id' => ['required', 'exists:users,id']]);
        $community->members()->syncWithoutDetaching([$validated['user_id'] => ['role' => 'member']]);

        return back()->with('success', 'Member added.');
    }

    public function storePost(Request $request, Community $community)
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        $validated['community_id'] = $community->id;
        $validated['user_id'] = auth()->id();

        CommunityPost::create($validated);

        return back()->with('success', 'Post created.');
    }

    public function destroyPost(CommunityPost $post)
    {
        $post->delete();

        return back()->with('success', 'Post deleted.');
    }
}
