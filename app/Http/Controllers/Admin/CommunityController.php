<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\CommunityAnnouncement;
use App\Models\CommunityPost;
use App\Services\ActivityLogger;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CommunityController extends Controller
{
    use ScopesToCurrentUser;

    public function __construct(protected TelegramService $telegram) {}

    public function index(Request $request)
    {
        $communities = $this->owned(Community::query())->withCount('members', 'posts')->latest()->paginate(15);

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
        $this->authorizeOwner($community);
        $community->load(['members', 'posts.user', 'announcements' => fn ($q) => $q->latest()->limit(20)]);
        $learners = $this->ownedUsersQuery('learner')->orderBy('name')->get();

        return view('admin.communities.show', compact('community', 'learners'));
    }

    public function edit(Community $community)
    {
        $this->authorizeOwner($community);

        return view('admin.communities.edit', compact('community'));
    }

    public function update(Request $request, Community $community)
    {
        $this->authorizeOwner($community);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'requires_approval' => ['boolean'],
            'telegram_invite_url' => ['nullable', 'url', 'max:500'],
            'telegram_chat_id' => ['nullable', 'string', 'max:64'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['requires_approval'] = $request->boolean('requires_approval');
        $validated['telegram_push_enabled'] = $request->boolean('telegram_push_enabled');

        $community->update($validated);

        return redirect()->route('admin.communities.show', $community)->with('success', 'Community updated.');
    }

    public function destroy(Community $community)
    {
        $this->authorizeOwner($community);
        $community->delete();

        return redirect()->route('admin.communities.index')->with('success', 'Community deleted.');
    }

    public function addMember(Request $request, Community $community)
    {
        $this->authorizeOwner($community);
        $learnerIds = $this->ownedUsersQuery('learner')->pluck('id')->all();

        $validated = $request->validate([
            'user_id' => ['required', 'integer', Rule::in($learnerIds)],
        ]);
        $community->members()->syncWithoutDetaching([$validated['user_id'] => ['role' => 'member']]);

        return back()->with('success', 'Member added.');
    }

    public function storePost(Request $request, Community $community)
    {
        $this->authorizeOwner($community);
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
        $this->authorizeOwner($post->community);
        $post->delete();

        return back()->with('success', 'Post deleted.');
    }

    public function storeAnnouncement(Request $request, Community $community)
    {
        $this->authorizeOwner($community);
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'push_telegram' => ['boolean'],
        ]);

        $announcement = CommunityAnnouncement::create([
            'community_id' => $community->id,
            'created_by' => auth()->id(),
            'title' => $validated['title'],
            'body' => $validated['body'],
        ]);

        $message = 'Announcement created.';
        if ($request->boolean('push_telegram')) {
            $result = $this->telegram->pushAnnouncement($announcement, auth()->id());
            $message .= $result['ok'] ? ' Pushed to Telegram.' : ' Telegram: '.$result['message'];
        }

        return back()->with('success', $message);
    }

    public function pushAnnouncement(CommunityAnnouncement $announcement)
    {
        $this->authorizeOwner($announcement->community);
        $result = $this->telegram->pushAnnouncement($announcement, auth()->id());

        return back()->with($result['ok'] ? 'success' : 'error', $result['message']);
    }
}
