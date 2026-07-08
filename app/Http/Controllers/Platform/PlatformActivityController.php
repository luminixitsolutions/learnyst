<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class PlatformActivityController extends Controller
{
    public function index(Request $request)
    {
        $logs = ActivityLog::with('user')
            ->when($request->search, fn ($q, $s) => $q->where('action', 'like', "%{$s}%")->orWhere('description', 'like', "%{$s}%"))
            ->latest()
            ->paginate(40)
            ->withQueryString();

        return view('platform.activity.index', compact('logs'));
    }
}
