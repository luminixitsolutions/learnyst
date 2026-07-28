<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    use ScopesToCurrentUser;

    public function index(Request $request)
    {
        $query = $this->owned(Question::query())->with(['questionPool', 'creator']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('question_text', 'like', "%{$search}%")
                    ->orWhere('correct_answer', 'like', "%{$search}%");
            });
        }

        if ($request->filled('question_type')) {
            $query->where('question_type', $request->question_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sort = $request->get('sort', 'latest');
        $query = match ($sort) {
            'oldest' => $query->oldest(),
            default => $query->latest(),
        };

        $questions = $query->limit(500)->get();

        return view('admin.questions.index', compact('questions', 'sort'));
    }

    public function destroy(Question $question)
    {
        $this->authorizeOwner($question);
        $text = str($question->question_text)->limit(50);
        $pool = $question->questionPool;

        $question->delete();

        if ($pool && $pool->questions_count > 0) {
            $pool->decrement('questions_count');
        }

        ActivityLogger::log('question_deleted', "Question deleted: {$text}");

        return redirect()
            ->route('admin.questions.index')
            ->with('success', 'Question deleted.');
    }
}
