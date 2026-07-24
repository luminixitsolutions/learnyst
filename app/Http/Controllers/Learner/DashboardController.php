<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\CourseEnrollment;
use App\Models\Resource;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $enrollmentsQuery = CourseEnrollment::with('course')
            ->where('user_id', $user->id)
            ->where('status', 'active');

        $activeCount = (clone $enrollmentsQuery)->count();
        $enrollments = (clone $enrollmentsQuery)->latest()->take(6)->get();
        $avgProgress = (int) round((float) (clone $enrollmentsQuery)->avg('progress'));

        $certificates = $user->certificates()->with('course')->latest()->take(3)->get();
        $certificateCount = $user->certificates()->count();
        $resources = Resource::where('status', 'published')->latest()->take(4)->get();
        $ordersCount = $user->orders()->count();

        return view('learner.dashboard', compact(
            'enrollments',
            'certificates',
            'resources',
            'activeCount',
            'avgProgress',
            'certificateCount',
            'ordersCount'
        ));
    }

    public function certificates()
    {
        $certificates = Auth::user()->certificates()->with('course')->latest()->paginate(12);

        return view('learner.certificates', compact('certificates'));
    }
}
