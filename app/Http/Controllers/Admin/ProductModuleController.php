<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ProductModuleController extends Controller
{
    public function show(string $module)
    {
        $modules = config('product-modules', []);
        abort_unless(isset($modules[$module]), 404);

        return view('admin.products.module', [
            'module' => $modules[$module],
            'slug' => $module,
        ]);
    }
}
