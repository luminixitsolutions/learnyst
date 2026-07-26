<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Bundle;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EnrollmentController extends Controller
{
    use ScopesToCurrentUser;

    protected function authorizeEnrollment(CourseEnrollment $enrollment): void
    {
        abort_unless(
            $this->ownedEnrollmentsConstraint(CourseEnrollment::query()->whereKey($enrollment->id))->exists(),
            403
        );
    }

    public function index(Request $request)
    {
        $query = CourseEnrollment::with(['user', 'course', 'batch', 'bundle'])->latest();
        $this->ownedEnrollmentsConstraint($query);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
        }
        if ($request->filled('type')) {
            $query->where('enrollment_type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $enrollments = $query->paginate(20)->withQueryString();
        $learners = $this->visibleLearnersQuery()->orderBy('name')->get();
        $courses = $this->owned(Course::query())->where('status', 'published')->orderBy('title')->get();
        $batches = Batch::whereIn('course_id', $this->ownedCourseIds())->orderBy('title')->get();
        $bundles = $this->owned(Bundle::query())->where('status', 'published')->orderBy('title')->get();

        return view('admin.enrollments.index', compact('enrollments', 'learners', 'courses', 'batches', 'bundles'));
    }

    public function store(Request $request)
    {
        $ownedLearnerIds = $this->visibleLearnersQuery()->pluck('id');
        $ownedCourseIds = $this->ownedCourseIds();
        $ownedBatchIds = $this->ownedBatchIds();
        $ownedBundleIds = $this->ownedBundleIds();

        $validated = $request->validate([
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['required', Rule::in($ownedLearnerIds)],
            'enrollment_type' => ['required', 'in:course,batch,bundle'],
            'course_id' => ['required_if:enrollment_type,course', 'nullable', Rule::in($ownedCourseIds)],
            'batch_id' => ['required_if:enrollment_type,batch', 'nullable', Rule::in($ownedBatchIds)],
            'bundle_id' => ['required_if:enrollment_type,bundle', 'nullable', Rule::in($ownedBundleIds)],
            'access_starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date'],
            'status' => ['required', 'in:active,expired,revoked'],
            'course_quiz_score' => ['nullable', 'numeric', 'min:0'],
            'mock_test_score' => ['nullable', 'numeric', 'min:0'],
            'test_series_score' => ['nullable', 'numeric', 'min:0'],
            'bundle_quiz_score' => ['nullable', 'numeric', 'min:0'],
        ]);

        $meta = array_filter([
            'course_quiz_score' => $validated['course_quiz_score'] ?? null,
            'mock_test_score' => $validated['mock_test_score'] ?? null,
            'test_series_score' => $validated['test_series_score'] ?? null,
            'bundle_quiz_score' => $validated['bundle_quiz_score'] ?? null,
        ], fn ($v) => $v !== null && $v !== '');

        $created = 0;
        foreach ($validated['user_ids'] as $userId) {
            $data = [
                'user_id' => $userId,
                'enrollment_type' => $validated['enrollment_type'],
                'course_id' => $validated['course_id'] ?? null,
                'batch_id' => $validated['batch_id'] ?? null,
                'bundle_id' => $validated['bundle_id'] ?? null,
                'status' => $validated['status'],
                'access_starts_at' => $validated['access_starts_at'] ?? now(),
                'expires_at' => $validated['expires_at'] ?? null,
                'enrolled_at' => now(),
                'meta' => $meta ?: null,
            ];

            $match = ['user_id' => $userId, 'enrollment_type' => $validated['enrollment_type']];
            if ($validated['enrollment_type'] === 'course') {
                $match['course_id'] = $validated['course_id'];
            } elseif ($validated['enrollment_type'] === 'batch') {
                $match['batch_id'] = $validated['batch_id'];
            } else {
                $match['bundle_id'] = $validated['bundle_id'];
            }

            CourseEnrollment::updateOrCreate($match, $data);

            if ($validated['enrollment_type'] === 'batch' && $validated['batch_id']) {
                Batch::find($validated['batch_id'])?->learners()->syncWithoutDetaching([$userId => ['status' => 'active']]);
            }

            if ($validated['enrollment_type'] === 'bundle' && $validated['bundle_id']) {
                $bundle = Bundle::with('courses')->find($validated['bundle_id']);
                foreach ($bundle?->courses ?? [] as $course) {
                    CourseEnrollment::updateOrCreate(
                        ['user_id' => $userId, 'enrollment_type' => 'course', 'course_id' => $course->id],
                        ['status' => 'active', 'enrolled_at' => now(), 'access_starts_at' => $data['access_starts_at'], 'expires_at' => $data['expires_at']]
                    );
                }
            }

            $created++;
        }

        ActivityLogger::log('enrollment_created', "{$created} enrollment(s) assigned");

        return back()->with('success', "{$created} enrollment(s) assigned successfully.");
    }

    public function update(Request $request, CourseEnrollment $enrollment)
    {
        $this->authorizeEnrollment($enrollment);

        $validated = $request->validate([
            'status' => ['required', 'in:active,expired,revoked'],
            'access_starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $enrollment->update($validated);
        ActivityLogger::log('enrollment_updated', 'Enrollment updated', $enrollment);

        return back()->with('success', 'Enrollment updated.');
    }

    public function destroy(CourseEnrollment $enrollment)
    {
        $this->authorizeEnrollment($enrollment);
        ActivityLogger::log('enrollment_removed', 'Enrollment removed', $enrollment);
        $enrollment->delete();

        return back()->with('success', 'Enrollment removed.');
    }

    public function history(User $learner)
    {
        abort_unless(
            $this->visibleLearnersQuery()->whereKey($learner->id)->exists(),
            403,
            'You do not have access to this resource.'
        );

        $enrollments = CourseEnrollment::with(['course', 'batch', 'bundle', 'order'])
            ->where('user_id', $learner->id);
        $this->ownedEnrollmentsConstraint($enrollments);
        $enrollments = $enrollments->latest()->paginate(20);

        return view('admin.enrollments.history', compact('learner', 'enrollments'));
    }
}
