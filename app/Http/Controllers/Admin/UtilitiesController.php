<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\MockTest;
use App\Models\ProductCopyLog;
use App\Models\TestSeries;

class UtilitiesController extends Controller
{
    public function index()
    {
        $utilities = collect(config('utilities.hub', []))->map(function ($item) {
            $item['url'] = route($item['route']);

            return $item;
        });

        return view('admin.utilities.index', compact('utilities'));
    }

    public function copyProduct()
    {
        $copyTypes = collect(config('utilities.copy_types', []))->map(function ($type) {
            if (! empty($type['route'])) {
                $type['url'] = route($type['route']);
            }

            return $type;
        });
        $history = ProductCopyLog::with('creator')->latest()->paginate(10);

        return view('admin.utilities.copy-product', compact('copyTypes', 'history'));
    }

    public function copyCourse()
    {
        $courses = Course::with(['sections' => fn ($query) => $query->orderBy('sort_order')])
            ->orderBy('title')
            ->get(['id', 'title']);

        return view('admin.utilities.copy-course', compact('courses'));
    }

    public function copyMockTest()
    {
        $mockTests = MockTest::orderBy('title')->get(['id', 'title']);

        return view('admin.utilities.copy-mock-test', compact('mockTests'));
    }

    public function copyTestSeries()
    {
        $testSeries = TestSeries::with(['sections' => fn ($query) => $query->orderBy('sort_order')])
            ->orderBy('title')
            ->get(['id', 'title']);

        return view('admin.utilities.copy-test-series', compact('testSeries'));
    }
}
