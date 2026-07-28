<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\CourseLesson;
use App\Models\MediaAccessToken;
use App\Services\DrmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MediaStreamController extends Controller
{
    public function __construct(protected DrmService $drm) {}

    public function stream(Request $request, string $token)
    {
        abort_unless($request->hasValidSignature(), 403);

        $record = $this->drm->resolveToken($token);
        abort_unless($record && $record->user_id === Auth::id(), 403);

        $lesson = CourseLesson::findOrFail($record->course_lesson_id);
        $path = $lesson->file_path ?? $lesson->video_path ?? null;

        // Prefer public disk URL file; stream local storage when available
        $url = $lesson->fileUrl();
        if ($url && str_starts_with($url, 'http') && ! str_contains($url, '/storage/')) {
            return redirect()->away($url);
        }

        $relative = $lesson->file_path ?? null;
        if ($relative && Storage::disk('public')->exists($relative)) {
            $record->touch();

            return Storage::disk('public')->response($relative);
        }

        if ($url) {
            return redirect()->away($url);
        }

        abort(404, 'Media not found.');
    }

    public function heartbeat(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'seconds' => ['required', 'integer', 'min:1', 'max:120'],
        ]);

        $record = MediaAccessToken::where('token', $validated['token'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $this->drm->recordWatch($record, (int) $validated['seconds']);

        return response()->json([
            'ok' => $record->fresh()->isValid(),
            'watched_seconds' => $record->fresh()->watched_seconds,
        ]);
    }
}
