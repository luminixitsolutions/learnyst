<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CourseEnrollment;
use App\Models\LiveClassAttendance;
use App\Models\Order;
use App\Models\ScheduledEvent;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    protected function linkedLearners()
    {
        return Auth::user()->linkedLearners()->orderBy('name')->get();
    }

    protected function linkedIds(): array
    {
        return Auth::user()->linkedLearners()->pluck('users.id')->all();
    }

    protected function assertLinked(int $learnerId): User
    {
        $learner = Auth::user()->linkedLearners()->where('users.id', $learnerId)->first();
        abort_unless($learner, 403);

        return $learner;
    }

    public function index()
    {
        $learners = $this->linkedLearners();
        $ids = $learners->pluck('id')->all();

        $summaries = $learners->map(function (User $learner) {
            $enrollments = CourseEnrollment::where('user_id', $learner->id)->where('status', 'active')->get();
            $avgProgress = $enrollments->avg('progress') ?? 0;
            $attendance = LiveClassAttendance::where('user_id', $learner->id)->count();
            $upcoming = ScheduledEvent::query()
                ->where('type', 'class')
                ->where('starts_at', '>=', now())
                ->where('status', '!=', 'cancelled')
                ->whereIn('course_id', $enrollments->pluck('course_id')->filter())
                ->count();

            return [
                'learner' => $learner,
                'courses' => $enrollments->count(),
                'progress' => round((float) $avgProgress, 1),
                'attendance' => $attendance,
                'upcoming' => $upcoming,
            ];
        });

        $notifications = empty($ids)
            ? collect()
            : UserNotification::query()->whereIn('user_id', $ids)->latest()->take(10)->get();

        $pendingFees = empty($ids)
            ? 0
            : Order::query()->whereIn('user_id', $ids)->whereIn('payment_status', ['pending', 'failed'])->count();

        $upcomingClasses = empty($ids)
            ? collect()
            : ScheduledEvent::query()
                ->with('course')
                ->where('type', 'class')
                ->where('starts_at', '>=', now())
                ->where('status', '!=', 'cancelled')
                ->whereIn('course_id', CourseEnrollment::whereIn('user_id', $ids)->pluck('course_id'))
                ->orderBy('starts_at')
                ->take(8)
                ->get();

        return view('parent.dashboard', compact('summaries', 'notifications', 'pendingFees', 'upcomingClasses'));
    }

    public function learners()
    {
        $learners = $this->linkedLearners();

        return view('parent.learners', compact('learners'));
    }

    public function learnerShow(User $learner)
    {
        $this->assertLinked($learner->id);
        $enrollments = CourseEnrollment::with('course')->where('user_id', $learner->id)->latest()->get();
        $attendanceCount = LiveClassAttendance::where('user_id', $learner->id)->count();
        $certificates = Certificate::where('user_id', $learner->id)->count();

        return view('parent.learner-show', compact('learner', 'enrollments', 'attendanceCount', 'certificates'));
    }

    public function attendance(Request $request)
    {
        $ids = $this->linkedIds();

        if ($request->filled('learner_id')) {
            $this->assertLinked((int) $request->learner_id);
            $ids = [(int) $request->learner_id];
        }

        $rows = LiveClassAttendance::with(['user', 'event.course'])
            ->whereIn('user_id', $ids)
            ->latest('attended_at')
            ->paginate(40)
            ->withQueryString();

        $learners = $this->linkedLearners();

        return view('parent.attendance', compact('rows', 'learners'));
    }

    public function performance()
    {
        $learners = $this->linkedLearners();
        $data = $learners->map(function (User $learner) {
            $enrollments = CourseEnrollment::with('course')->where('user_id', $learner->id)->get();

            return [
                'learner' => $learner,
                'enrollments' => $enrollments,
                'avg' => round((float) ($enrollments->avg('progress') ?? 0), 1),
            ];
        });

        return view('parent.performance', compact('data'));
    }

    public function progress()
    {
        $ids = $this->linkedIds();
        $enrollments = CourseEnrollment::with(['course', 'user'])
            ->whereIn('user_id', $ids ?: [0])
            ->latest()
            ->paginate(40);

        return view('parent.progress', compact('enrollments'));
    }

    public function fees()
    {
        $ids = $this->linkedIds();
        $orders = Order::with(['user', 'items'])
            ->whereIn('user_id', $ids ?: [0])
            ->latest()
            ->paginate(30);

        $outstanding = Order::whereIn('user_id', $ids ?: [0])
            ->whereIn('payment_status', ['pending', 'failed'])
            ->sum('total');

        return view('parent.fees', compact('orders', 'outstanding'));
    }

    public function notifications()
    {
        $ids = $this->linkedIds();
        $notifications = UserNotification::whereIn('user_id', $ids ?: [0])->latest()->paginate(40);

        return view('parent.notifications', compact('notifications'));
    }

    public function certificates()
    {
        $ids = $this->linkedIds();
        $certificates = Certificate::with(['user', 'course'])
            ->whereIn('user_id', $ids ?: [0])
            ->latest('issued_at')
            ->paginate(30);

        return view('parent.certificates', compact('certificates'));
    }

    public function downloadCertificate(Certificate $certificate): StreamedResponse|\Illuminate\Http\RedirectResponse
    {
        $this->assertLinked((int) $certificate->user_id);

        if ($certificate->pdf_path && Storage::disk('public')->exists($certificate->pdf_path)) {
            return Storage::disk('public')->download($certificate->pdf_path, $certificate->certificate_number.'.pdf');
        }

        return redirect()->route('certificates.verify', ['number' => $certificate->certificate_number])
            ->with('info', 'PDF not available — opened verification page.');
    }
}
