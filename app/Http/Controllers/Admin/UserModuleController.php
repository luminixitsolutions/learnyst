<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class UserModuleController extends Controller
{
    public function show(string $module)
    {
        $modules = config('user-modules', []);
        abort_unless(isset($modules[$module]), 404);

        return view('admin.users.module', [
            'module' => $modules[$module],
            'slug' => $module,
        ]);
    }
}
