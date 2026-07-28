<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Instructor\Concerns\ScopesToInstructor;
use App\Models\Certificate;
use App\Models\CourseEnrollment;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CertificateController extends Controller
{
    use ScopesToInstructor;

    public function index()
    {
        $certificates = Certificate::query()
            ->with(['user', 'course'])
            ->whereIn('course_id', $this->assignedCourseIds())
            ->latest('issued_at')
            ->paginate(30);

        return view('instructor.certificates.index', compact('certificates'));
    }

    public function create()
    {
        $courses = $this->assignedCoursesQuery()->orderBy('title')->get();
        $learnerIds = $this->enrolledLearnerIds();
        $learners = User::whereIn('id', $learnerIds)->orderBy('name')->get(['id', 'name', 'email']);

        return view('instructor.certificates.create', compact('courses', 'learners'));
    }

    public function store(Request $request)
    {
        $courseIds = $this->assignedCourseIds()->all();
        $learnerIds = $this->enrolledLearnerIds()->all();

        $validated = $request->validate([
            'user_id' => ['required', Rule::in($learnerIds)],
            'course_id' => ['required', Rule::in($courseIds)],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        abort_unless(
            CourseEnrollment::where('user_id', $validated['user_id'])
                ->where('course_id', $validated['course_id'])
                ->exists(),
            422,
            'Learner is not enrolled in this course.'
        );

        $certificate = Certificate::create([
            'certificate_number' => 'INS-'.strtoupper(Str::random(10)),
            'user_id' => $validated['user_id'],
            'course_id' => $validated['course_id'],
            'issued_at' => now(),
            'status' => 'valid',
        ]);

        ActivityLogger::log(
            'instructor_certificate_issued',
            "Certificate issued {$certificate->certificate_number}".($validated['notes'] ? ' — '.$validated['notes'] : ''),
            $certificate
        );

        return redirect()->route('instructor.certificates.index')->with('success', 'Certificate issued.');
    }
}
