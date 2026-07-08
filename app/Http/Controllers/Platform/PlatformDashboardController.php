<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Course;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PlatformDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'companies' => 1,
            'total_users' => User::count(),
            'total_courses' => Course::count(),
            'platform_revenue' => Order::where('payment_status', 'paid')->sum('total'),
            'active_learners' => User::where('role_id', DB::table('roles')->where('slug', 'learner')->value('id'))->count(),
        ];

        $recentActivity = ActivityLog::with('user')->latest()->take(10)->get();

        return view('platform.dashboard', compact('stats', 'recentActivity'));
    }
}
