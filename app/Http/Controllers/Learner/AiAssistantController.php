<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\AiChatMessage;
use App\Models\AiGeneration;
use App\Models\CourseEnrollment;
use App\Services\AiService;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiAssistantController extends Controller
{
    public function __construct(
        protected AiService $ai,
        protected GamificationService $gamification
    ) {}

    public function chat()
    {
        $user = Auth::user();
        $instituteId = $this->gamification->resolveInstituteId($user);
        $messages = AiChatMessage::where('user_id', $user->id)->latest()->limit(40)->get()->reverse()->values();

        return view('learner.ai.chat', compact('messages', 'instituteId'));
    }

    public function sendChat(Request $request)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:4000'],
            'course_id' => ['nullable', 'integer'],
        ]);

        $user = Auth::user();
        $instituteId = $this->gamification->resolveInstituteId($user);

        AiChatMessage::create([
            'user_id' => $user->id,
            'created_by' => $instituteId,
            'course_id' => $validated['course_id'] ?? null,
            'role' => 'user',
            'content' => $validated['message'],
        ]);

        $history = AiChatMessage::where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get()
            ->reverse()
            ->map(fn ($m) => ['role' => $m->role, 'content' => $m->content])
            ->values()
            ->all();

        $config = $this->ai->getConfig($instituteId);
        $reply = $this->ai->chat($config, 'You are a helpful teaching assistant.', $validated['message'], array_slice($history, 0, -1));

        AiChatMessage::create([
            'user_id' => $user->id,
            'created_by' => $instituteId,
            'course_id' => $validated['course_id'] ?? null,
            'role' => 'assistant',
            'content' => $reply,
        ]);

        return back();
    }

    public function planner()
    {
        $user = Auth::user();
        $instituteId = $this->gamification->resolveInstituteId($user);
        $plans = AiGeneration::where('user_id', $user->id)
            ->where('feature', 'study_planner')
            ->latest()
            ->limit(10)
            ->get();

        $courses = CourseEnrollment::with('course')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->get()
            ->pluck('course')
            ->filter();

        return view('learner.ai.planner', compact('plans', 'courses', 'instituteId'));
    }

    public function createPlanner(Request $request)
    {
        $validated = $request->validate([
            'prompt' => ['required', 'string', 'max:4000'],
            'course_id' => ['nullable', 'integer'],
        ]);

        $user = Auth::user();
        $instituteId = $this->gamification->resolveInstituteId($user);

        $this->ai->generate(
            $user,
            'study_planner',
            $validated['prompt'],
            $instituteId,
            $validated['course_id'] ?? null,
            'My study plan'
        );

        return back()->with('success', 'Study plan drafted.');
    }
}
