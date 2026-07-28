<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\AiGeneration;
use App\Models\Course;
use App\Services\ActivityLogger;
use App\Services\AiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiCenterController extends Controller
{
    use ScopesToCurrentUser;

    public function __construct(protected AiService $ai) {}

    public function index()
    {
        $config = $this->ai->getConfig(Auth::id());
        $drafts = $this->owned(AiGeneration::query())->latest()->get();
        $features = AiGeneration::features();

        return view('admin.ai.index', compact('config', 'drafts', 'features'));
    }

    public function settings(Request $request)
    {
        $validated = $request->validate([
            'base_url' => ['nullable', 'url'],
            'model' => ['nullable', 'string', 'max:80'],
            'api_key' => ['nullable', 'string', 'max:500'],
            'enabled' => ['boolean'],
        ]);

        $this->ai->saveConfig([
            'base_url' => $validated['base_url'] ?? null,
            'model' => $validated['model'] ?? null,
            'api_key' => $validated['api_key'] ?? null,
            'enabled' => $request->boolean('enabled', true),
        ], Auth::id());

        return back()->with('success', 'AI settings saved. API key stored encrypted.');
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'feature' => ['required', 'in:'.implode(',', array_keys(AiGeneration::features()))],
            'prompt' => ['required', 'string', 'max:8000'],
            'title' => ['nullable', 'string', 'max:180'],
            'course_id' => ['nullable', 'integer'],
        ]);

        if (! empty($validated['course_id']) && ! $this->ownedCourseIds()->contains((int) $validated['course_id'])) {
            abort(403);
        }

        $generation = $this->ai->generate(
            Auth::user(),
            $validated['feature'],
            $validated['prompt'],
            Auth::id(),
            $validated['course_id'] ?? null,
            $validated['title'] ?? null
        );

        ActivityLogger::log('ai_generated', "AI {$generation->feature} draft created", $generation);

        return redirect()->route('admin.ai.show', $generation)->with('success', 'Draft generated for review.');
    }

    public function show(AiGeneration $ai)
    {
        $this->authorizeOwner($ai);
        $courses = $this->owned(Course::query())->orderBy('title')->get(['id', 'title']);

        return view('admin.ai.show', ['generation' => $ai, 'courses' => $courses]);
    }

    public function updateStatus(Request $request, AiGeneration $ai)
    {
        $this->authorizeOwner($ai);
        $validated = $request->validate([
            'status' => ['required', 'in:draft,approved,published,rejected'],
        ]);

        $ai->update([
            'status' => $validated['status'],
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Draft status updated. Publish into courses/quizzes manually from the approved content.');
    }
}
