<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\InstructorTask;
use App\Models\Payment;
use App\Models\ScheduledEvent;

class DashboardController extends Controller
{
    use ScopesToCurrentUser;

    public function index()
    {
        $ownedOrderIds = $this->ownedOrdersQuery()->select('orders.id');
        $ownedInstructorIds = $this->ownedUsersQuery('instructor')->pluck('id');
        $ownedCourseIds = $this->ownedCourseIds();

        $stats = [
            'total_sales' => $this->ownedOrdersQuery()->where('payment_status', 'paid')->sum('total'),
            'total_learners' => $this->visibleLearnersQuery()->count(),
            'total_courses' => $this->owned(Course::query())->count(),
            'total_payments' => Payment::where('status', 'success')
                ->whereIn('order_id', $ownedOrderIds)
                ->count(),
            'pending_orders' => $this->ownedOrdersQuery()->where('payment_status', 'pending')->count(),
        ];

        $monthlySales = $this->ownedOrdersQuery()
            ->where('payment_status', 'paid')
            ->whereYear('created_at', now()->year)
            ->selectRaw('MONTH(created_at) as month, SUM(total) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        $recentLearners = $this->visibleLearnersQuery()
            ->latest()
            ->take(5)
            ->get();

        $recentOrders = $this->ownedOrdersQuery()->with('user')->latest()->take(5)->get();

        $pendingTasks = InstructorTask::where('status', 'pending')
            ->where(function ($q) use ($ownedCourseIds, $ownedInstructorIds) {
                $q->whereIn('course_id', $ownedCourseIds)
                    ->orWhereIn('user_id', $ownedInstructorIds);
            })
            ->latest()
            ->take(5)
            ->get();

        $scheduledEvents = $this->owned(ScheduledEvent::query())
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'monthlySales', 'recentLearners', 'recentOrders', 'pendingTasks', 'scheduledEvents'
        ));
    }
}
