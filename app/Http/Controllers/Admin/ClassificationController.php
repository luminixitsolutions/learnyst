<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ClassificationController extends Controller
{
    public function index()
    {
        $options = collect(config('classification', []))->map(function ($option) {
            $option['url'] = route($option['route']);

            return $option;
        });

        return view('admin.classification.index', compact('options'));
    }
}
