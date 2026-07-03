<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Community;
use Illuminate\Support\Facades\Auth;

class CommunityController extends Controller
{
    public function index()
    {
        $communities = Community::where('is_active', true)
            ->whereHas('members', fn ($q) => $q->where('user_id', Auth::id()))
            ->withCount('posts')
            ->get();

        return view('learner.communities.index', compact('communities'));
    }

    public function show(Community $community)
    {
        $community->members()->where('user_id', Auth::id())->firstOrFail();
        $community->load(['posts.user']);

        return view('learner.communities.show', compact('community'));
    }
}
