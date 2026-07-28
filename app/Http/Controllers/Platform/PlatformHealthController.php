<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PlatformHealthController extends Controller
{
    public function index()
    {
        $failedJobsCount = 0;
        $failedJobs = collect();

        if (Schema::hasTable('failed_jobs')) {
            $failedJobsCount = DB::table('failed_jobs')->count();
            $failedJobs = DB::table('failed_jobs')
                ->orderByDesc('failed_at')
                ->limit(20)
                ->get(['id', 'uuid', 'queue', 'exception', 'failed_at']);
        }

        $info = [
            'php' => PHP_VERSION,
            'laravel' => app()->version(),
            'env' => config('app.env'),
            'debug' => config('app.debug') ? 'On' : 'Off',
            'queue' => config('queue.default'),
            'cache' => config('cache.default'),
            'session' => config('session.driver'),
            'maintenance' => app()->isDownForMaintenance() ? 'Laravel down' : 'Up',
            'soft_maintenance' => \App\Models\Setting::get('maintenance_mode', '0', 'platform') === '1' ? 'Flag on' : 'Flag off',
        ];

        return view('platform.health.index', compact('failedJobsCount', 'failedJobs', 'info'));
    }

    public function clearCache()
    {
        Cache::flush();
        Artisan::call('cache:clear');

        ActivityLogger::log('system_cache_cleared', 'Application cache cleared from platform health');

        return back()->with('success', 'Application cache cleared.');
    }

    public function clearConfig()
    {
        Artisan::call('config:clear');

        ActivityLogger::log('system_config_cleared', 'Config cache cleared from platform health');

        return back()->with('success', 'Config cache cleared.');
    }

    public function clearViews()
    {
        Artisan::call('view:clear');

        ActivityLogger::log('system_views_cleared', 'Compiled views cleared from platform health');

        return back()->with('success', 'Compiled views cleared.');
    }

    public function clearRoutes()
    {
        Artisan::call('route:clear');

        ActivityLogger::log('system_routes_cleared', 'Route cache cleared from platform health');

        return back()->with('success', 'Route cache cleared.');
    }
}
