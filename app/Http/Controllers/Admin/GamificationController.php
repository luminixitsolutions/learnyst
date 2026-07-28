<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\Challenge;
use App\Models\Course;
use App\Models\LiveClassAttendance;
use App\Models\ScheduledEvent;
use App\Models\User;
use App\Models\XpRule;
use App\Services\ActivityLogger;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class GamificationController extends Controller
{
    use ScopesToCurrentUser;

    public function __construct(protected GamificationService $gamification) {}

    public function rules()
    {
        $this->gamification->ensureRulesFor(Auth::id());
        $rules = $this->owned(XpRule::query())->orderBy('action_key')->get();

        return view('admin.gamification.rules', compact('rules'));
    }

    public function updateRules(Request $request)
    {
        $validated = $request->validate([
            'rules' => ['required', 'array'],
            'rules.*.id' => ['required', 'integer'],
            'rules.*.points' => ['required', 'integer', 'min:0', 'max:10000'],
            'rules.*.daily_cap' => ['nullable', 'integer', 'min:1'],
            'rules.*.is_active' => ['nullable', 'boolean'],
        ]);

        foreach ($validated['rules'] as $row) {
            $rule = $this->owned(XpRule::query())->whereKey($row['id'])->first();
            if (! $rule) {
                continue;
            }
            $rule->update([
                'points' => $row['points'],
                'daily_cap' => $row['daily_cap'] ?? null,
                'is_active' => (bool) ($row['is_active'] ?? false),
            ]);
        }

        return back()->with('success', 'XP rules updated.');
    }

    public function badges()
    {
        $badges = $this->owned(Badge::query())->withCount('users')->latest()->paginate(20);

        return view('admin.gamification.badges', compact('badges'));
    }

    public function storeBadge(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:60'],
            'criteria_type' => ['required', 'in:xp_total,level,streak,lesson_complete_count,quiz_pass_count'],
            'criteria_value' => ['required', 'integer', 'min:1'],
            'xp_reward' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $validated['created_by'] = Auth::id();
        $validated['slug'] = Str::slug($validated['name']).'-'.Str::random(4);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['xp_reward'] = $validated['xp_reward'] ?? 0;

        $badge = Badge::create($validated);
        ActivityLogger::log('badge_created', "Badge {$badge->name} created", $badge);

        return back()->with('success', 'Badge created.');
    }

    public function destroyBadge(Badge $badge)
    {
        $this->authorizeOwner($badge);
        $badge->delete();

        return back()->with('success', 'Badge deleted.');
    }

    public function challenges()
    {
        $challenges = $this->owned(Challenge::query())->latest()->paginate(20);
        $actions = collect(XpRule::defaultRules())->pluck('label', 'action_key');

        return view('admin.gamification.challenges', compact('challenges', 'actions'));
    }

    public function storeChallenge(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string'],
            'action_key' => ['required', 'string', 'max:60'],
            'target_count' => ['required', 'integer', 'min:1'],
            'xp_reward' => ['nullable', 'integer', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['boolean'],
        ]);

        $validated['created_by'] = Auth::id();
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['xp_reward'] = $validated['xp_reward'] ?? 0;

        $challenge = Challenge::create($validated);
        ActivityLogger::log('challenge_created', "Challenge {$challenge->title} created", $challenge);

        return back()->with('success', 'Challenge created.');
    }

    public function destroyChallenge(Challenge $challenge)
    {
        $this->authorizeOwner($challenge);
        $challenge->delete();

        return back()->with('success', 'Challenge deleted.');
    }

    public function leaderboard(Request $request)
    {
        $courses = $this->owned(Course::query())->orderBy('title')->get(['id', 'title']);
        $courseId = $request->integer('course_id') ?: null;

        $rows = $courseId && $this->ownedCourseIds()->contains($courseId)
            ? $this->gamification->courseLeaderboard(Auth::id(), $courseId)
            : $this->gamification->globalLeaderboard(Auth::id());

        return view('admin.gamification.leaderboard', compact('rows', 'courses', 'courseId'));
    }

    public function attendances(ScheduledEvent $liveClass)
    {
        $this->authorizeOwner($liveClass);
        $liveClass->load(['course']);

        $learners = $this->visibleLearnersQuery()
            ->when($liveClass->course_id, function ($q) use ($liveClass) {
                $q->whereHas('enrollments', fn ($e) => $e->where('course_id', $liveClass->course_id)->where('status', 'active'));
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $attended = LiveClassAttendance::where('scheduled_event_id', $liveClass->id)
            ->pluck('user_id')
            ->all();

        return view('admin.gamification.attendances', compact('liveClass', 'learners', 'attended'));
    }

    public function markAttendance(Request $request, ScheduledEvent $liveClass)
    {
        $this->authorizeOwner($liveClass);

        $validated = $request->validate([
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => [Rule::in($this->visibleLearnersQuery()->pluck('id')->all())],
        ]);

        foreach ($validated['user_ids'] as $userId) {
            $attendance = LiveClassAttendance::firstOrCreate(
                [
                    'scheduled_event_id' => $liveClass->id,
                    'user_id' => $userId,
                ],
                [
                    'marked_by' => Auth::id(),
                    'attended_at' => now(),
                ]
            );

            if ($attendance->wasRecentlyCreated) {
                $learner = User::find($userId);
                if ($learner) {
                    $this->gamification->award(
                        $learner,
                        'live_attendance',
                        Auth::id(),
                        $liveClass,
                        $liveClass->course_id
                    );
                }
            }
        }

        return back()->with('success', 'Attendance marked and XP awarded.');
    }
}
