<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\TestSeries;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestSeriesController extends Controller
{
    use ScopesToCurrentUser;

    public function index(Request $request)
    {
        $query = $this->owned(TestSeries::query())->with('creator')->latest();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $testSeries = $query->get();

        return view('admin.test-series.index', compact('testSeries'));
    }

    public function create()
    {
        return view('admin.test-series.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:60'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'is_free' => ['sometimes', 'boolean'],
        ]);

        $isFree = $request->boolean('is_free');

        if ($isFree) {
            $validated['price'] = 0;
            $validated['is_free'] = true;
        } else {
            $validated['is_free'] = false;
            $request->validate([
                'price' => ['required', 'numeric', 'min:0'],
            ]);
            $validated['price'] = $request->input('price');
        }

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'draft';

        $testSeries = TestSeries::create($validated);

        ActivityLogger::log('test_series_created', "Test series {$testSeries->title} created", $testSeries);

        return redirect()
            ->route('admin.test-series.index')
            ->with('success', 'Test series created successfully.');
    }

    public function edit(TestSeries $testSeries)
    {
        $this->authorizeOwner($testSeries);

        return view('admin.test-series.edit', compact('testSeries'));
    }

    public function update(Request $request, TestSeries $testSeries)
    {
        $this->authorizeOwner($testSeries);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:60'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'is_free' => ['sometimes', 'boolean'],
            'status' => ['required', 'in:draft,published,unpublished'],
        ]);

        $isFree = $request->boolean('is_free');

        if ($isFree) {
            $validated['price'] = 0;
            $validated['is_free'] = true;
        } else {
            $validated['is_free'] = false;
            $request->validate([
                'price' => ['required', 'numeric', 'min:0'],
            ]);
            $validated['price'] = $request->input('price');
        }

        $testSeries->update($validated);

        ActivityLogger::log('test_series_updated', "Test series {$testSeries->title} updated", $testSeries);

        return redirect()
            ->route('admin.test-series.index')
            ->with('success', 'Test series updated successfully.');
    }

    public function destroy(TestSeries $testSeries)
    {
        $this->authorizeOwner($testSeries);
        $title = $testSeries->title;
        $testSeries->delete();

        ActivityLogger::log('test_series_deleted', "Test series {$title} deleted");

        return redirect()
            ->route('admin.test-series.index')
            ->with('success', 'Test series deleted.');
    }
}
