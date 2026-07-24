<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\MockTest;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MockTestController extends Controller
{
    use ScopesToCurrentUser;

    public function index(Request $request)
    {
        $query = $this->owned(MockTest::query())->with('creator')->latest();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('quiz_type')) {
            $query->where('quiz_type', $request->quiz_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $mockTests = $query->paginate(15)->withQueryString();

        return view('admin.mock-tests.index', compact('mockTests'));
    }

    public function create()
    {
        $templates = config('mock-test-templates', []);

        return view('admin.mock-tests.create', compact('templates'));
    }

    public function store(Request $request)
    {
        $templates = array_keys(config('mock-test-templates', []));

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:60'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'is_free' => ['sometimes', 'boolean'],
            'quiz_type' => ['required', Rule::in(['online', 'offline'])],
            'template' => ['nullable', 'string', Rule::in($templates)],
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

        if ($validated['quiz_type'] === 'online' && empty($validated['template'])) {
            return back()
                ->withInput()
                ->withErrors(['template' => 'Please select a template for the online quiz.']);
        }

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'draft';

        $mockTest = MockTest::create($validated);

        ActivityLogger::log('mock_test_created', "Mock test {$mockTest->title} created", $mockTest);

        return redirect()
            ->route('admin.mock-tests.index')
            ->with('success', 'Mock test created successfully.');
    }

    public function destroy(MockTest $mockTest)
    {
        $this->authorizeOwner($mockTest);
        $title = $mockTest->title;
        $mockTest->delete();

        ActivityLogger::log('mock_test_deleted', "Mock test {$title} deleted");

        return redirect()
            ->route('admin.mock-tests.index')
            ->with('success', 'Mock test deleted.');
    }
}
