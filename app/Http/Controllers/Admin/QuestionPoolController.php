<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\QuestionPool;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestionPoolController extends Controller
{
    use ScopesToCurrentUser;

    public function index(Request $request)
    {
        $query = $this->owned(QuestionPool::query())->with('creator');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sort = $request->get('sort', 'latest');
        $query = match ($sort) {
            'title' => $query->orderBy('title'),
            default => $query->latest(),
        };

        $questionPools = $query->get();

        return view('admin.question-pools.index', compact('questionPools', 'sort'));
    }

    public function create()
    {
        return view('admin.question-pools.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:60'],
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'draft';

        $pool = QuestionPool::create($validated);

        ActivityLogger::log('question_pool_created', "Question pool {$pool->title} created", $pool);

        return redirect()
            ->route('admin.question-pools.index')
            ->with('success', 'Question pool created successfully.');
    }

    public function destroy(QuestionPool $questionPool)
    {
        $this->authorizeOwner($questionPool);
        $title = $questionPool->title;
        $questionPool->delete();

        ActivityLogger::log('question_pool_deleted', "Question pool {$title} deleted");

        return redirect()
            ->route('admin.question-pools.index')
            ->with('success', 'Question pool deleted.');
    }
}
