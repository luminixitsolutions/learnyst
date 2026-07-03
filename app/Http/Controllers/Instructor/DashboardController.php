<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\InstructorTask;
use App\Models\ScheduledEvent;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $courses = $user->courses()->withCount('enrollments')->get();
        $batches = Batch::where('instructor_id', $user->id)->withCount('learners')->get();
        $tasks = InstructorTask::where('user_id', $user->id)->where('status', '!=', 'completed')->latest()->take(5)->get();
        $events = ScheduledEvent::where('starts_at', '>=', now())->orderBy('starts_at')->take(5)->get();

        return view('instructor.dashboard', compact('courses', 'batches', 'tasks', 'events'));
    }
}
