<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GamificationController extends Controller
{
    public function __construct(protected GamificationService $gamification) {}

    public function profile()
    {
        $user = Auth::user();
        $instituteId = $this->gamification->resolveInstituteId($user);
        $profile = $instituteId
            ? $this->gamification->profileFor($user, $instituteId)
            : null;

        $transactions = $profile
            ? $profile->transactions()->latest()->limit(30)->get()
            : collect();

        $badges = $user->badges()->latest('badge_user.awarded_at')->get();

        $challenges = $instituteId
            ? Challenge::where('created_by', $instituteId)->where('is_active', true)->latest()->get()
            : collect();

        $challengeProgress = $instituteId
            ? \DB::table('challenge_user')->where('user_id', $user->id)->get()->keyBy('challenge_id')
            : collect();

        $leaderboard = $instituteId
            ? $this->gamification->globalLeaderboard($instituteId, 10)
            : collect();

        return view('learner.gamification.profile', compact(
            'profile', 'transactions', 'badges', 'challenges', 'challengeProgress', 'leaderboard'
        ));
    }

    public function leaderboard(Request $request)
    {
        $user = Auth::user();
        $instituteId = $this->gamification->resolveInstituteId($user);
        abort_unless($instituteId, 404);

        $courseId = $request->integer('course_id') ?: null;
        $courses = Course::whereIn(
            'id',
            CourseEnrollment::where('user_id', $user->id)->where('status', 'active')->pluck('course_id')
        )->orderBy('title')->get(['id', 'title']);

        $rows = $courseId
            ? $this->gamification->courseLeaderboard($instituteId, $courseId)
            : $this->gamification->globalLeaderboard($instituteId);

        return view('learner.gamification.leaderboard', compact('rows', 'courses', 'courseId'));
    }
}
