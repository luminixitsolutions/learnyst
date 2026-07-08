<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;

class PlatformUserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->latest()->paginate(30);
        $roles = Role::orderBy('name')->get();

        return view('platform.users.index', compact('users', 'roles'));
    }
}
