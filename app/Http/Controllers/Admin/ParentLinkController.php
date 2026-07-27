<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;

class ParentLinkController extends Controller
{
    use ScopesToCurrentUser;

    public function index()
    {
        $parentRole = Role::where('slug', 'parent')->first();
        $learnerRole = Role::where('slug', 'learner')->first();

        $parents = User::query()
            ->when($parentRole, fn ($q) => $q->where('role_id', $parentRole->id))
            ->when(! $parentRole, fn ($q) => $q->whereRaw('1 = 0'))
            ->latest()
            ->paginate(15, ['*'], 'parents_page');

        $learners = $this->visibleLearnersQuery()
            ->when($learnerRole, fn ($q) => $q->where('role_id', $learnerRole->id))
            ->orderBy('name')
            ->limit(100)
            ->get(['id', 'name', 'email']);

        return view('admin.parents.index', compact('parents', 'learners'));
    }
}
