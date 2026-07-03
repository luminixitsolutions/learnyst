<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\InstructorTask;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ScheduledEvent;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $learnerRoleId = DB::table('roles')->where('slug', 'learner')->value('id');

        $stats = [
            'total_sales' => Order::where('payment_status', 'paid')->sum('total'),
            'total_learners' => User::where('role_id', $learnerRoleId)->count(),
            'total_courses' => Course::count(),
            'total_payments' => Payment::where('status', 'success')->count(),
            'pending_orders' => Order::where('payment_status', 'pending')->count(),
        ];

        $monthlySales = Order::where('payment_status', 'paid')
            ->whereYear('created_at', now()->year)
            ->selectRaw('MONTH(created_at) as month, SUM(total) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        $recentLearners = User::where('role_id', $learnerRoleId)
            ->latest()
            ->take(5)
            ->get();

        $recentOrders = Order::with('user')->latest()->take(5)->get();
        $pendingTasks = InstructorTask::where('status', 'pending')->latest()->take(5)->get();
        $scheduledEvents = ScheduledEvent::where('starts_at', '>=', now())->orderBy('starts_at')->take(5)->get();

        return view('admin.dashboard', compact(
            'stats', 'monthlySales', 'recentLearners', 'recentOrders', 'pendingTasks', 'scheduledEvents'
        ));
    }
}
