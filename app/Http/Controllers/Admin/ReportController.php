<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function sales(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->get('to', now()->format('Y-m-d'));

        $orders = Order::with('user')
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$from, $to . ' 23:59:59'])
            ->latest()
            ->get();

        $total = $orders->sum('total');

        return view('admin.reports.sales', compact('orders', 'total', 'from', 'to'));
    }

    public function learners()
    {
        $learnerRoleId = DB::table('roles')->where('slug', 'learner')->value('id');

        $learners = User::where('role_id', $learnerRoleId)
            ->withCount('enrollments')
            ->orderByDesc('total_spent')
            ->paginate(30);

        return view('admin.reports.learners', compact('learners'));
    }

    public function courses()
    {
        $courses = Course::withCount('enrollments')
            ->orderByDesc('enrollment_count')
            ->paginate(20);

        return view('admin.reports.courses', compact('courses'));
    }

    public function payments(Request $request)
    {
        $payments = Payment::with(['user', 'order'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(30);

        return view('admin.reports.payments', compact('payments'));
    }

    public function batches()
    {
        $batches = Batch::withCount('learners')->with('course')->get();

        return view('admin.reports.batches', compact('batches'));
    }

    public function enrollments()
    {
        $enrollments = CourseEnrollment::with(['user', 'course', 'batch', 'bundle'])
            ->latest()
            ->paginate(30);

        return view('admin.reports.enrollments', compact('enrollments'));
    }

    public function bundles()
    {
        $bundles = \App\Models\Bundle::withCount(['courses', 'enrollments'])->get();

        return view('admin.reports.bundles', compact('bundles'));
    }

    public function certificates()
    {
        $certificates = Certificate::with(['user', 'course'])->latest()->paginate(30);

        return view('admin.reports.certificates', compact('certificates'));
    }
}
