<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\Challenge;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\GamificationProfile;
use App\Models\User;
use App\Models\XpRule;
use App\Models\XpTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GamificationService
{
    public function ensureRulesFor(int $instituteUserId): void
    {
        foreach (XpRule::defaultRules() as $rule) {
            XpRule::firstOrCreate(
                ['created_by' => $instituteUserId, 'action_key' => $rule['action_key']],
                [
                    'label' => $rule['label'],
                    'points' => $rule['points'],
                    'daily_cap' => $rule['daily_cap'],
                    'is_active' => true,
                ]
            );
        }
    }

    public function profileFor(User $user, ?int $instituteUserId = null): GamificationProfile
    {
        $instituteUserId = $instituteUserId ?: $this->resolveInstituteId($user);

        return GamificationProfile::firstOrCreate(
            [
                'user_id' => $user->id,
                'created_by' => $instituteUserId,
            ],
            [
                'xp' => 0,
                'level' => 1,
                'current_streak' => 0,
                'longest_streak' => 0,
            ]
        );
    }

    public function award(
        User $user,
        string $actionKey,
        ?int $instituteUserId = null,
        ?Model $source = null,
        ?int $courseId = null,
        array $meta = []
    ): ?XpTransaction {
        $instituteUserId = $instituteUserId ?: $this->resolveInstituteId($user);
        if (! $instituteUserId) {
            return null;
        }

        $this->ensureRulesFor($instituteUserId);

        $rule = XpRule::where('created_by', $instituteUserId)
            ->where('action_key', $actionKey)
            ->where('is_active', true)
            ->first();

        if (! $rule || $rule->points <= 0) {
            return null;
        }

        return DB::transaction(function () use ($user, $actionKey, $instituteUserId, $source, $courseId, $meta, $rule) {
            $profile = $this->profileFor($user, $instituteUserId);

            if ($rule->daily_cap) {
                $todayCount = XpTransaction::where('user_id', $user->id)
                    ->where('created_by', $instituteUserId)
                    ->where('action_key', $actionKey)
                    ->whereDate('created_at', today())
                    ->count();
                if ($todayCount >= $rule->daily_cap) {
                    return null;
                }
            }

            if ($source) {
                $exists = XpTransaction::where('user_id', $user->id)
                    ->where('action_key', $actionKey)
                    ->where('source_type', $source::class)
                    ->where('source_id', $source->getKey())
                    ->exists();
                if ($exists) {
                    return null;
                }
            }

            $txn = XpTransaction::create([
                'gamification_profile_id' => $profile->id,
                'user_id' => $user->id,
                'created_by' => $instituteUserId,
                'action_key' => $actionKey,
                'points' => $rule->points,
                'source_type' => $source ? $source::class : null,
                'source_id' => $source?->getKey(),
                'course_id' => $courseId,
                'meta' => $meta ?: null,
            ]);

            $profile->xp += $rule->points;
            $profile->level = GamificationProfile::levelForXp($profile->xp);
            $profile->save();

            $this->progressChallenges($user, $actionKey, $instituteUserId);
            $this->evaluateBadges($user, $instituteUserId);

            return $txn;
        });
    }

    public function awardForLessonComplete(User $user, CourseLesson $lesson, Course $course): ?XpTransaction
    {
        $action = match ($lesson->lesson_type) {
            'quiz' => 'quiz_pass',
            'assignment' => 'assignment_submit',
            default => 'lesson_complete',
        };

        return $this->award(
            $user,
            $action,
            $course->created_by,
            $lesson,
            $course->id,
            ['lesson_type' => $lesson->lesson_type]
        );
    }

    public function recordLogin(User $user): void
    {
        if (! in_array($user->role?->slug, ['learner', 'alumni'], true)) {
            return;
        }

        $instituteUserId = $this->resolveInstituteId($user);
        if (! $instituteUserId) {
            return;
        }

        $profile = $this->profileFor($user, $instituteUserId);
        $today = today();
        $last = $profile->last_activity_date;

        if ($last && $last->isSameDay($today)) {
            return;
        }

        if ($last && $last->isSameDay($today->copy()->subDay())) {
            $profile->current_streak += 1;
        } else {
            $profile->current_streak = 1;
        }

        $profile->longest_streak = max($profile->longest_streak, $profile->current_streak);
        $profile->last_activity_date = $today;
        $profile->save();

        $this->award($user, 'login_streak', $instituteUserId, null, null, [
            'streak' => $profile->current_streak,
        ]);

        $this->evaluateBadges($user, $instituteUserId);
    }

    public function progressChallenges(User $user, string $actionKey, int $instituteUserId): void
    {
        $challenges = Challenge::where('created_by', $instituteUserId)
            ->where('action_key', $actionKey)
            ->where('is_active', true)
            ->get()
            ->filter(fn (Challenge $c) => $c->isOpen());

        foreach ($challenges as $challenge) {
            $pivot = DB::table('challenge_user')->where([
                'challenge_id' => $challenge->id,
                'user_id' => $user->id,
            ])->first();

            $progress = ($pivot->progress ?? 0) + 1;
            $completedAt = $pivot->completed_at ?? null;

            if (! $completedAt && $progress >= $challenge->target_count) {
                $completedAt = now();
                if ($challenge->xp_reward > 0) {
                    $profile = $this->profileFor($user, $instituteUserId);
                    $profile->xp += $challenge->xp_reward;
                    $profile->level = GamificationProfile::levelForXp($profile->xp);
                    $profile->save();

                    XpTransaction::create([
                        'gamification_profile_id' => $profile->id,
                        'user_id' => $user->id,
                        'created_by' => $instituteUserId,
                        'action_key' => 'challenge_complete',
                        'points' => $challenge->xp_reward,
                        'source_type' => Challenge::class,
                        'source_id' => $challenge->id,
                        'meta' => ['challenge' => $challenge->title],
                    ]);
                }
            }

            DB::table('challenge_user')->updateOrInsert(
                ['challenge_id' => $challenge->id, 'user_id' => $user->id],
                [
                    'progress' => $progress,
                    'completed_at' => $completedAt,
                    'updated_at' => now(),
                    'created_at' => $pivot->created_at ?? now(),
                ]
            );
        }
    }

    public function evaluateBadges(User $user, int $instituteUserId): void
    {
        $profile = $this->profileFor($user, $instituteUserId);
        $badges = Badge::where('created_by', $instituteUserId)->where('is_active', true)->get();

        foreach ($badges as $badge) {
            if ($user->badges()->where('badges.id', $badge->id)->exists()) {
                continue;
            }

            $met = match ($badge->criteria_type) {
                'xp_total' => $profile->xp >= $badge->criteria_value,
                'level' => $profile->level >= $badge->criteria_value,
                'streak' => $profile->current_streak >= $badge->criteria_value || $profile->longest_streak >= $badge->criteria_value,
                'action_count' => XpTransaction::where('user_id', $user->id)
                    ->where('created_by', $instituteUserId)
                    ->where('action_key', $badge->icon ?: 'lesson_complete')
                    ->count() >= $badge->criteria_value,
                'lesson_complete_count' => XpTransaction::where('user_id', $user->id)
                    ->where('created_by', $instituteUserId)
                    ->where('action_key', 'lesson_complete')
                    ->count() >= $badge->criteria_value,
                'quiz_pass_count' => XpTransaction::where('user_id', $user->id)
                    ->where('created_by', $instituteUserId)
                    ->where('action_key', 'quiz_pass')
                    ->count() >= $badge->criteria_value,
                default => false,
            };

            if (! $met) {
                continue;
            }

            $user->badges()->attach($badge->id, ['awarded_at' => now()]);

            if ($badge->xp_reward > 0) {
                $profile->xp += $badge->xp_reward;
                $profile->level = GamificationProfile::levelForXp($profile->xp);
                $profile->save();

                XpTransaction::create([
                    'gamification_profile_id' => $profile->id,
                    'user_id' => $user->id,
                    'created_by' => $instituteUserId,
                    'action_key' => 'badge_reward',
                    'points' => $badge->xp_reward,
                    'source_type' => Badge::class,
                    'source_id' => $badge->id,
                    'meta' => ['badge' => $badge->name],
                ]);
            }
        }
    }

    public function globalLeaderboard(int $instituteUserId, int $limit = 50)
    {
        return GamificationProfile::with('user')
            ->where('created_by', $instituteUserId)
            ->orderByDesc('xp')
            ->limit($limit)
            ->get();
    }

    public function courseLeaderboard(int $instituteUserId, int $courseId, int $limit = 50)
    {
        $rows = XpTransaction::query()
            ->selectRaw('user_id, SUM(points) as total_xp')
            ->where('created_by', $instituteUserId)
            ->where('course_id', $courseId)
            ->groupBy('user_id')
            ->orderByDesc('total_xp')
            ->limit($limit)
            ->get();

        $users = User::whereIn('id', $rows->pluck('user_id'))->get()->keyBy('id');

        return $rows->map(function ($row) use ($users) {
            $row->user = $users->get($row->user_id);
            $row->setRelation('user', $row->user);

            return $row;
        });
    }

    public function resolveInstituteId(User $user): ?int
    {
        if ($user->created_by) {
            return (int) $user->created_by;
        }

        $courseOwner = DB::table('course_enrollments')
            ->join('courses', 'courses.id', '=', 'course_enrollments.course_id')
            ->where('course_enrollments.user_id', $user->id)
            ->whereNotNull('courses.created_by')
            ->value('courses.created_by');

        return $courseOwner ? (int) $courseOwner : null;
    }
}
