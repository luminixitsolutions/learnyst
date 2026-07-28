<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\AiGeneration;
use App\Services\AiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiController extends Controller
{
    public function __construct(protected AiService $ai) {}

    public function index()
    {
        $user = Auth::user();
        $instituteId = $user->created_by ?: $user->id;
        $drafts = AiGeneration::where('user_id', $user->id)->latest()->paginate(15);
        $features = collect(AiGeneration::features())->except(['doubt_chat', 'study_planner']);

        return view('instructor.ai.index', compact('drafts', 'features', 'instituteId'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'feature' => ['required', 'in:course_outline,quiz,notes,assignment,performance'],
            'prompt' => ['required', 'string', 'max:8000'],
            'title' => ['nullable', 'string', 'max:180'],
        ]);

        $user = Auth::user();
        $generation = $this->ai->generate(
            $user,
            $validated['feature'],
            $validated['prompt'],
            $user->created_by ?: $user->id,
            null,
            $validated['title'] ?? null
        );

        return redirect()->route('instructor.ai.index')->with('success', 'Draft #'.$generation->id.' ready for admin review.');
    }
}
