<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;

class AlumniController extends Controller
{
    use ScopesToCurrentUser;

    public function index()
    {
        $alumniRole = Role::where('slug', 'alumni')->first();

        $alumni = User::query()
            ->when($alumniRole, fn ($q) => $q->where('role_id', $alumniRole->id))
            ->when(! $alumniRole, fn ($q) => $q->whereRaw('1 = 0'))
            ->where(function ($q) {
                $q->where('created_by', $this->currentUserId())
                    ->orWhereHas('certificates', fn ($c) => $c->whereIn('course_id', $this->ownedCourseIds()));
            })
            ->withCount('certificates')
            ->latest()
            ->paginate(20);

        return view('admin.alumni.index', compact('alumni'));
    }
}
