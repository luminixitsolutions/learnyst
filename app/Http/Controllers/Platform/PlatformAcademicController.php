<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Company;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\OrderItem;
use App\Models\ScheduledEvent;
use App\Services\PlatformAcademicService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PlatformAcademicController extends Controller
{
    public function __construct(protected PlatformAcademicService $academic) {}

    public function courses(Request $request)
    {
        $companies = Company::orderBy('name')->get(['id', 'name', 'owner_user_id']);
        $byOwner = $this->academic->companiesByOwner();

        $query = Course::query()->with(['category', 'creator'])->withCount([
            'enrollments as enrollments_count',
            'enrollments as active_enrollments_count' => fn (Builder $q) => $q->where('status', 'active'),
        ])->latest();
        $this->applyCourseFilters($query, $request);

        $base = Course::query();
        $this->applyCourseFilters($base, $request);

        $stats = [
            'total' => (clone $base)->count(),
            'published' => (clone $base)->where('status', 'published')->count(),
            'draft' => (clone $base)->where('status', 'draft')->count(),
            'enrollments' => (int) CourseEnrollment::query()
                ->whereHas('course', function (Builder $c) use ($request) {
                    if ($request->filled('company_id')) {
                        $this->academic->scopeCoursesForCompany($c, (int) $request->company_id);
                    }
                })
                ->count(),
        ];

        $courses = $query->paginate(30)->withQueryString();

        $courseIds = $courses->getCollection()->pluck('id')->all();
        $revenueByCourse = $this->revenueByCourseIds($courseIds);

        $courses->getCollection()->transform(function (Course $course) use ($byOwner, $revenueByCourse) {
            $course->setAttribute('institute', $this->academic->resolveCompanyForCourse($course, $byOwner));
            $course->setAttribute('revenue', (float) ($revenueByCourse[$course->id] ?? 0));

            return $course;
        });

        return view('platform.academic.courses', compact('courses', 'companies', 'stats'));
    }

    public function courseShow(Course $course)
    {
        $course->load(['category', 'creator', 'instructors']);
        $course->loadCount([
            'enrollments as enrollments_count',
            'enrollments as active_enrollments_count' => fn (Builder $q) => $q->where('status', 'active'),
        ]);
        $institute = $this->academic->resolveCompanyForCourse($course);
        $revenue = $this->academic->courseRevenue($course->id);
        $recentEnrollments = CourseEnrollment::query()
            ->with('user')
            ->where('course_id', $course->id)
            ->latest('enrolled_at')
            ->limit(15)
            ->get();

        return view('platform.academic.course-show', compact('course', 'institute', 'revenue', 'recentEnrollments'));
    }

    public function coursesExport(Request $request): StreamedResponse
    {
        $byOwner = $this->academic->companiesByOwner();
        $query = Course::query()->with(['category', 'creator'])->withCount('enrollments as enrollments_count')->latest();
        $this->applyCourseFilters($query, $request);

        return response()->streamDownload(function () use ($query, $byOwner) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id', 'title', 'status', 'institute', 'enrollments', 'price', 'revenue', 'created_at']);
            $query->chunk(100, function ($chunk) use ($out, $byOwner) {
                $ids = $chunk->pluck('id')->all();
                $revenueByCourse = $this->revenueByCourseIds($ids);
                foreach ($chunk as $course) {
                    $institute = $this->academic->resolveCompanyForCourse($course, $byOwner);
                    fputcsv($out, [
                        $course->id,
                        $course->title,
                        $course->status,
                        $institute?->name,
                        $course->enrollments_count,
                        $course->price,
                        $revenueByCourse[$course->id] ?? 0,
                        $course->created_at?->toDateTimeString(),
                    ]);
                }
            });
            fclose($out);
        }, 'platform-courses-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function enrollments(Request $request)
    {
        $companies = Company::orderBy('name')->get(['id', 'name']);
        $byOwner = $this->academic->companiesByOwner();
        $courses = Course::query()->orderBy('title')->get(['id', 'title', 'created_by']);

        $query = CourseEnrollment::query()->with(['user', 'course'])->latest('enrolled_at');
        $this->applyEnrollmentFilters($query, $request);

        $base = CourseEnrollment::query();
        $this->applyEnrollmentFilters($base, $request);

        $stats = [
            'total' => (clone $base)->count(),
            'active' => (clone $base)->where('status', 'active')->count(),
            'expired' => (clone $base)->where('status', 'expired')->count(),
            'revoked' => (clone $base)->where('status', 'revoked')->count(),
        ];

        $enrollments = $query->paginate(30)->withQueryString();
        $enrollments->getCollection()->transform(function (CourseEnrollment $enrollment) use ($byOwner) {
            $enrollment->setAttribute('institute', $this->academic->resolveCompanyForEnrollment($enrollment, $byOwner));

            return $enrollment;
        });

        return view('platform.academic.enrollments', compact('enrollments', 'companies', 'courses', 'stats'));
    }

    public function enrollmentShow(CourseEnrollment $enrollment)
    {
        $enrollment->load(['user', 'course', 'batch', 'bundle', 'order']);
        $institute = $this->academic->resolveCompanyForEnrollment($enrollment);

        return view('platform.academic.enrollment-show', compact('enrollment', 'institute'));
    }

    public function enrollmentsExport(Request $request): StreamedResponse
    {
        $byOwner = $this->academic->companiesByOwner();
        $query = CourseEnrollment::query()->with(['user', 'course'])->latest('enrolled_at');
        $this->applyEnrollmentFilters($query, $request);

        return response()->streamDownload(function () use ($query, $byOwner) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id', 'student', 'email', 'course', 'institute', 'status', 'type', 'amount', 'enrolled_at', 'progress']);
            $query->chunk(200, function ($chunk) use ($out, $byOwner) {
                foreach ($chunk as $enrollment) {
                    $institute = $this->academic->resolveCompanyForEnrollment($enrollment, $byOwner);
                    fputcsv($out, [
                        $enrollment->id,
                        $enrollment->user?->name,
                        $enrollment->user?->email,
                        $enrollment->course?->title,
                        $institute?->name,
                        $enrollment->status,
                        $enrollment->enrollment_type,
                        $enrollment->amount,
                        $enrollment->enrolled_at?->toDateTimeString(),
                        $enrollment->progress,
                    ]);
                }
            });
            fclose($out);
        }, 'platform-enrollments-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function liveClasses(Request $request)
    {
        $companies = Company::orderBy('name')->get(['id', 'name']);
        $byOwner = $this->academic->companiesByOwner();
        $window = $request->query('window', 'upcoming');

        $query = ScheduledEvent::query()
            ->with(['course', 'instructor', 'batch'])
            ->where('type', 'class')
            ->orderBy('starts_at');
        $this->applyLiveClassFilters($query, $request, $window);

        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        $baseToday = ScheduledEvent::query()->where('type', 'class')->whereBetween('starts_at', [$todayStart, $todayEnd]);
        $baseUpcoming = ScheduledEvent::query()->where('type', 'class')
            ->where('starts_at', '>=', now())
            ->whereIn('status', ['scheduled', 'live']);
        if ($request->filled('company_id')) {
            $this->academic->scopeEventsForCompany($baseToday, (int) $request->company_id);
            $this->academic->scopeEventsForCompany($baseUpcoming, (int) $request->company_id);
        }

        $stats = [
            'today' => (clone $baseToday)->count(),
            'upcoming' => (clone $baseUpcoming)->count(),
            'live' => ScheduledEvent::query()->where('type', 'class')->where('status', 'live')
                ->when($request->filled('company_id'), fn ($q) => $this->academic->scopeEventsForCompany($q, (int) $request->company_id))
                ->count(),
            'cancelled' => ScheduledEvent::query()->where('type', 'class')->where('status', 'cancelled')
                ->when($request->filled('company_id'), fn ($q) => $this->academic->scopeEventsForCompany($q, (int) $request->company_id))
                ->count(),
        ];

        $events = $query->paginate(30)->withQueryString();
        $events->getCollection()->transform(function (ScheduledEvent $event) use ($byOwner) {
            $event->setAttribute('institute', $this->academic->resolveCompanyForEvent($event, $byOwner));

            return $event;
        });

        return view('platform.academic.live-classes', compact('events', 'companies', 'stats', 'window'));
    }

    public function liveClassShow(ScheduledEvent $event)
    {
        abort_unless($event->type === 'class', 404);

        $event->load(['course', 'instructor', 'batch']);
        $institute = $this->academic->resolveCompanyForEvent($event);

        return view('platform.academic.live-class-show', compact('event', 'institute'));
    }

    public function liveClassesExport(Request $request): StreamedResponse
    {
        $byOwner = $this->academic->companiesByOwner();
        $window = $request->query('window', 'upcoming');
        $query = ScheduledEvent::query()->with(['course', 'instructor'])->where('type', 'class')->orderBy('starts_at');
        $this->applyLiveClassFilters($query, $request, $window);

        return response()->streamDownload(function () use ($query, $byOwner) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id', 'title', 'institute', 'course', 'instructor', 'status', 'starts_at', 'ends_at', 'platform']);
            $query->chunk(200, function ($chunk) use ($out, $byOwner) {
                foreach ($chunk as $event) {
                    $institute = $this->academic->resolveCompanyForEvent($event, $byOwner);
                    fputcsv($out, [
                        $event->id,
                        $event->title,
                        $institute?->name,
                        $event->course?->title,
                        $event->instructor?->name,
                        $event->status,
                        $event->starts_at?->toDateTimeString(),
                        $event->ends_at?->toDateTimeString(),
                        $event->platform,
                    ]);
                }
            });
            fclose($out);
        }, 'platform-live-classes-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function certificates(Request $request)
    {
        $companies = Company::orderBy('name')->get(['id', 'name']);
        $byOwner = $this->academic->companiesByOwner();
        $courses = Course::query()->orderBy('title')->get(['id', 'title']);

        $query = Certificate::query()->with(['user', 'course', 'template'])->latest('issued_at');
        $this->applyCertificateFilters($query, $request);

        $base = Certificate::query();
        $this->applyCertificateFilters($base, $request);

        $stats = [
            'total' => (clone $base)->count(),
            'valid' => (clone $base)->where('status', 'valid')->count(),
            'expiring' => (clone $base)->where('status', 'expiring_soon')->count(),
            'expired' => (clone $base)->whereIn('status', ['expired', 'renewal_due'])->count(),
        ];

        $certificates = $query->paginate(30)->withQueryString();
        $certificates->getCollection()->transform(function (Certificate $certificate) use ($byOwner) {
            $certificate->setAttribute('institute', $this->academic->resolveCompanyForCertificate($certificate, $byOwner));

            return $certificate;
        });

        return view('platform.academic.certificates', compact('certificates', 'companies', 'courses', 'stats'));
    }

    public function certificateShow(Certificate $certificate)
    {
        $certificate->load(['user', 'course', 'template']);
        $institute = $this->academic->resolveCompanyForCertificate($certificate);

        return view('platform.academic.certificate-show', compact('certificate', 'institute'));
    }

    public function certificatesExport(Request $request): StreamedResponse
    {
        $byOwner = $this->academic->companiesByOwner();
        $query = Certificate::query()->with(['user', 'course'])->latest('issued_at');
        $this->applyCertificateFilters($query, $request);

        return response()->streamDownload(function () use ($query, $byOwner) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['certificate_number', 'student', 'email', 'course', 'institute', 'status', 'issued_at', 'expires_at']);
            $query->chunk(200, function ($chunk) use ($out, $byOwner) {
                foreach ($chunk as $certificate) {
                    $institute = $this->academic->resolveCompanyForCertificate($certificate, $byOwner);
                    fputcsv($out, [
                        $certificate->certificate_number,
                        $certificate->user?->name,
                        $certificate->user?->email,
                        $certificate->course?->title,
                        $institute?->name,
                        $certificate->status,
                        $certificate->issued_at?->toDateTimeString(),
                        $certificate->expires_at?->toDateTimeString(),
                    ]);
                }
            });
            fclose($out);
        }, 'platform-certificates-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    protected function revenueByCourseIds(array $courseIds): array
    {
        if ($courseIds === []) {
            return [];
        }

        return OrderItem::query()
            ->select('course_id', DB::raw('SUM(total) as revenue'))
            ->whereIn('course_id', $courseIds)
            ->whereHas('order', fn (Builder $o) => $o->where('payment_status', 'paid'))
            ->groupBy('course_id')
            ->pluck('revenue', 'course_id')
            ->map(fn ($v) => (float) $v)
            ->all();
    }

    protected function applyCourseFilters(Builder $query, Request $request): void
    {
        if ($request->filled('company_id')) {
            $this->academic->scopeCoursesForCompany($query, (int) $request->company_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }
    }

    protected function applyEnrollmentFilters(Builder $query, Request $request): void
    {
        if ($request->filled('company_id')) {
            $this->academic->scopeEnrollmentsForCompany($query, (int) $request->company_id);
        }
        if ($request->filled('course_id')) {
            $query->where('course_id', (int) $request->course_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from')) {
            $query->whereDate('enrolled_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('enrolled_at', '<=', $request->to);
        }
        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function (Builder $q) use ($search) {
                $q->whereHas('user', fn (Builder $u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('course', fn (Builder $c) => $c->where('title', 'like', "%{$search}%"));
            });
        }
    }

    protected function applyLiveClassFilters(Builder $query, Request $request, string $window): void
    {
        if ($request->filled('company_id')) {
            $this->academic->scopeEventsForCompany($query, (int) $request->company_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        match ($window) {
            'today' => $query->whereDate('starts_at', now()->toDateString()),
            'upcoming' => $query->where('starts_at', '>=', now()->startOfDay())
                ->whereIn('status', ['scheduled', 'live']),
            'past' => $query->where('starts_at', '<', now()->startOfDay()),
            default => null,
        };

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('course', fn (Builder $c) => $c->where('title', 'like', "%{$search}%"));
            });
        }
    }

    protected function applyCertificateFilters(Builder $query, Request $request): void
    {
        if ($request->filled('company_id')) {
            $this->academic->scopeCertificatesForCompany($query, (int) $request->company_id);
        }
        if ($request->filled('course_id')) {
            $query->where('course_id', (int) $request->course_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from')) {
            $query->whereDate('issued_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('issued_at', '<=', $request->to);
        }
        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('certificate_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn (Builder $u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('course', fn (Builder $c) => $c->where('title', 'like', "%{$search}%"));
            });
        }
    }
}
