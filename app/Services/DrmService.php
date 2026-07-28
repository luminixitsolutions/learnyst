<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CourseSetting;
use App\Models\LoginDevice;
use App\Models\MediaAccessToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DrmService
{
    public function policyFor(Course $course): array
    {
        $settings = $course->settings ?: CourseSetting::where('course_id', $course->id)->first();
        $drm = $settings?->drm_config ?? [];

        return array_merge([
            'enabled' => false,
            'signed_urls' => true,
            'url_ttl_minutes' => 60,
            'watermark' => true,
            'max_devices' => 3,
            'max_parallel_sessions' => 1,
            'max_watch_seconds_per_day' => null,
            'restrict_seeking' => false,
            'block_download' => true,
        ], is_array($drm) ? $drm : []);
    }

    public function issueMediaUrl(User $user, CourseLesson $lesson, Request $request): array
    {
        $course = $lesson->section->course;
        $policy = $this->policyFor($course);
        $fallback = $lesson->fileUrl() ?: ($lesson->embedSrc() ?: '');

        if (! ($policy['enabled'] ?? false) || ! ($policy['signed_urls'] ?? true)) {
            return ['url' => $fallback, 'token' => null];
        }

        $this->assertDeviceAllowed($user, $course, $request, $policy);

        $token = MediaAccessToken::create([
            'user_id' => $user->id,
            'course_lesson_id' => $lesson->id,
            'token' => Str::random(48),
            'expires_at' => now()->addMinutes((int) ($policy['url_ttl_minutes'] ?? 60)),
            'max_seconds' => $policy['max_watch_seconds_per_day'] ?? null,
            'device_id' => app(AuthSecurityService::class)->deviceId($request),
        ]);

        return [
            'url' => URL::temporarySignedRoute(
                'learner.media.stream',
                $token->expires_at,
                ['token' => $token->token]
            ),
            'token' => $token->token,
        ];
    }

    public function assertDeviceAllowed(User $user, Course $course, Request $request, ?array $policy = null): void
    {
        $policy ??= $this->policyFor($course);
        if (! ($policy['enabled'] ?? false)) {
            return;
        }

        $maxDevices = (int) ($policy['max_devices'] ?? 3);
        $deviceId = app(AuthSecurityService::class)->deviceId($request);

        $active = LoginDevice::where('user_id', $user->id)->whereNull('revoked_at')->count();
        $known = LoginDevice::where('user_id', $user->id)->where('device_id', $deviceId)->whereNull('revoked_at')->exists();

        if (! $known && $active >= $maxDevices) {
            throw ValidationException::withMessages([
                'device' => 'Device limit reached for this course. Revoke another device from Security settings.',
            ]);
        }

        $parallel = (int) ($policy['max_parallel_sessions'] ?? 1);
        if ($parallel > 0) {
            $others = MediaAccessToken::where('user_id', $user->id)
                ->whereNull('revoked_at')
                ->where('expires_at', '>', now())
                ->where('device_id', '!=', $deviceId)
                ->where('updated_at', '>', now()->subMinutes(5))
                ->count();

            if ($others >= $parallel) {
                throw ValidationException::withMessages([
                    'session' => 'Another device is currently streaming. Parallel playback is limited.',
                ]);
            }
        }
    }

    public function watermarkText(User $user): string
    {
        return trim($user->name.' · '.$user->email.' · #'.$user->id);
    }

    public function resolveToken(string $token): ?MediaAccessToken
    {
        $record = MediaAccessToken::where('token', $token)->first();
        if (! $record || ! $record->isValid()) {
            return null;
        }

        return $record;
    }

    public function recordWatch(MediaAccessToken $token, int $seconds): void
    {
        $token->increment('watched_seconds', max(0, $seconds));
        $token->touch();

        if ($token->max_seconds && $token->watched_seconds >= $token->max_seconds) {
            $token->update(['revoked_at' => now()]);
        }
    }
}
