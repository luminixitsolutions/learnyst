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

        $enrollments = CourseEnrollment::with('course')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->latest()
            ->take(6)
            ->get();

        $certificates = $user->certificates()->with('course')->latest()->take(3)->get();
        $resources = Resource::where('status', 'published')->latest()->take(4)->get();

        return view('learner.dashboard', compact('enrollments', 'certificates', 'resources'));
    }
}
