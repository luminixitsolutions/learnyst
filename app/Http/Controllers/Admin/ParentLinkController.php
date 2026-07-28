<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\ParentLearnerLink;
use App\Models\Role;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ParentLinkController extends Controller
{
    use ScopesToCurrentUser;

    public function index()
    {
        $parentRole = Role::where('slug', 'parent')->first();
        $learnerRole = Role::where('slug', 'learner')->first();

        $parents = User::query()
            ->when($parentRole, fn ($q) => $q->where('role_id', $parentRole->id))
            ->with(['linkedLearners'])
            ->orderBy('name')
            ->get();

        $parentOptions = User::query()
            ->when($parentRole, fn ($q) => $q->where('role_id', $parentRole->id))
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $learners = $this->visibleLearnersQuery()
            ->when($learnerRole, fn ($q) => $q->where('role_id', $learnerRole->id))
            ->orderBy('name')
            ->limit(100)
            ->get(['id', 'name', 'email']);

        $links = ParentLearnerLink::with(['parent', 'learner'])->latest()->get();

        return view('admin.parents.index', compact('parents', 'parentOptions', 'learners', 'links'));
    }

    public function store(Request $request)
    {
        $parentRole = Role::where('slug', 'parent')->value('id');
        $learnerRole = Role::where('slug', 'learner')->value('id');

        $validated = $request->validate([
            'parent_user_id' => ['required', 'exists:users,id', Rule::exists('users', 'id')->where('role_id', $parentRole)],
            'learner_user_id' => ['required', 'exists:users,id', Rule::exists('users', 'id')->where('role_id', $learnerRole)],
        ]);

        $companyId = Company::where('owner_user_id', Auth::id())->value('id')
            ?? Company::where('owner_user_id', Auth::user()->created_by)->value('id');

        $link = ParentLearnerLink::updateOrCreate(
            [
                'parent_user_id' => $validated['parent_user_id'],
                'learner_user_id' => $validated['learner_user_id'],
            ],
            [
                'company_id' => $companyId,
                'status' => 'active',
                'created_by' => Auth::id(),
            ]
        );

        ActivityLogger::log('parent_link_created', 'Parent linked to learner', $link, [
            'company_id' => $companyId,
        ]);

        return back()->with('success', 'Parent linked to learner.');
    }

    public function destroy(ParentLearnerLink $link)
    {
        $link->delete();
        ActivityLogger::log('parent_link_removed', 'Parent-learner link removed');

        return back()->with('success', 'Link removed.');
    }
}
