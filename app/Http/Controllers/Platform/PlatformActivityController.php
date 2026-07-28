<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Company;
use App\Models\LoginHistory;
use App\Models\Role;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PlatformActivityController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->filterMeta();
        $logs = $this->filteredQuery($request)
            ->with(['user.role', 'company'])
            ->latest()
            ->paginate(40)
            ->withQueryString();

        $stats = [
            'total' => ActivityLog::count(),
            'today' => ActivityLog::where('created_at', '>=', now()->startOfDay())->count(),
            'failed_logins_today' => ActivityLog::where('action', 'login_failed')
                ->where('created_at', '>=', now()->startOfDay())
                ->count(),
            'unique_ips_today' => (int) ActivityLog::where('created_at', '>=', now()->startOfDay())
                ->whereNotNull('ip_address')
                ->selectRaw('COUNT(DISTINCT ip_address) as c')
                ->value('c'),
        ];

        return view('platform.activity.index', compact('logs', 'filters', 'stats'));
    }

    public function show(ActivityLog $activityLog)
    {
        $activityLog->load(['user.role', 'company', 'subject']);

        return view('platform.activity.show', [
            'log' => $activityLog,
        ]);
    }

    public function loginAudit(Request $request)
    {
        $filters = $this->filterMeta();

        $activityQuery = ActivityLog::query()
            ->with(['user.role', 'company'])
            ->whereIn('action', ActivityLog::authActionTypes())
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->query('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->query('to')))
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', (int) $request->query('user_id')))
            ->when($request->filled('ip'), fn ($q) => $q->where('ip_address', 'like', '%'.$request->query('ip').'%'))
            ->when($request->filled('action'), fn ($q) => $q->where('action', $request->query('action')))
            ->when($request->filled('provider'), function ($q) use ($request) {
                $provider = $request->query('provider');
                $q->where(function ($inner) use ($provider) {
                    $inner->where('action', 'login_'.$provider)
                        ->orWhere('description', 'like', "%{$provider}%")
                        ->orWhere('properties->provider', $provider);
                });
            })
            ->latest();

        $logs = $activityQuery->paginate(40, ['*'], 'page')->withQueryString();

        $history = LoginHistory::query()
            ->with('user.role')
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->query('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->query('to')))
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', (int) $request->query('user_id')))
            ->when($request->filled('ip'), fn ($q) => $q->where('ip_address', 'like', '%'.$request->query('ip').'%'))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
            ->when($request->filled('provider'), fn ($q) => $q->where('provider', $request->query('provider')))
            ->latest()
            ->paginate(30, ['*'], 'history_page')
            ->withQueryString();

        $stats = [
            'success_today' => LoginHistory::where('status', 'success')->where('created_at', '>=', now()->startOfDay())->count(),
            'failed_today' => LoginHistory::where('status', 'failed')->where('created_at', '>=', now()->startOfDay())->count(),
            'blocked_today' => LoginHistory::where('status', 'blocked')->where('created_at', '>=', now()->startOfDay())->count(),
            'google_today' => LoginHistory::where('provider', 'google')->where('created_at', '>=', now()->startOfDay())->count()
                + ActivityLog::where('action', 'login_google')->where('created_at', '>=', now()->startOfDay())->count(),
        ];

        return view('platform.activity.login-audit', compact('logs', 'history', 'filters', 'stats'));
    }

    public function liveSessions(Request $request)
    {
        $threshold = now()->subMinutes(15)->timestamp;
        $sessions = collect();

        if (Schema::hasTable('sessions') && Schema::hasColumn('sessions', 'user_id')) {
            $rows = DB::table('sessions')
                ->whereNotNull('user_id')
                ->where('last_activity', '>=', $threshold)
                ->orderByDesc('last_activity')
                ->get();

            $users = User::with(['role', 'company'])
                ->whereIn('id', $rows->pluck('user_id')->unique()->filter())
                ->get()
                ->keyBy('id');

            $sessions = $rows->map(function ($row) use ($users) {
                $user = $users->get($row->user_id);

                return (object) [
                    'id' => $row->id,
                    'user' => $user,
                    'ip_address' => $row->ip_address,
                    'user_agent' => $row->user_agent,
                    'last_activity' => $row->last_activity,
                    'last_activity_at' => \Carbon\Carbon::createFromTimestamp($row->last_activity),
                ];
            })->filter(fn ($s) => $s->user !== null)->values();

            if ($request->filled('role')) {
                $sessions = $sessions->filter(fn ($s) => $s->user?->role?->slug === $request->query('role'))->values();
            }
            if ($search = trim((string) $request->query('search', ''))) {
                $sessions = $sessions->filter(function ($s) use ($search) {
                    return str_contains(strtolower($s->user?->name ?? ''), strtolower($search))
                        || str_contains(strtolower($s->user?->email ?? ''), strtolower($search))
                        || str_contains((string) $s->ip_address, $search);
                })->values();
            }
        }

        $roles = Role::orderBy('name')->get();

        return view('platform.activity.live-sessions', compact('sessions', 'roles'));
    }

    public function revokeSession(Request $request, string $session)
    {
        abort_unless(Schema::hasTable('sessions'), 404);

        $row = DB::table('sessions')->where('id', $session)->first();
        abort_unless($row, 404);

        DB::table('sessions')->where('id', $session)->delete();

        $user = $row->user_id ? User::find($row->user_id) : null;
        ActivityLogger::log(
            'session_revoked',
            'Live session revoked'.($user ? " for {$user->email}" : ''),
            $user,
            ['session_id' => $session, 'ip' => $row->ip_address]
        );

        return back()->with('success', 'Session revoked.');
    }

    public function export(Request $request): StreamedResponse
    {
        $filename = 'activity-monitor-'.now()->format('Ymd-His').'.csv';
        $query = $this->filteredQuery($request)->with(['user', 'company'])->latest();

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'id', 'created_at', 'action', 'description', 'user_id', 'user_name', 'user_email',
                'company_id', 'company_name', 'subject_type', 'subject_id', 'ip_address', 'user_agent', 'properties',
            ]);

            $query->chunk(200, function ($chunk) use ($out) {
                foreach ($chunk as $log) {
                    fputcsv($out, [
                        $log->id,
                        $log->created_at?->toDateTimeString(),
                        $log->action,
                        $log->description,
                        $log->user_id,
                        $log->user?->name,
                        $log->user?->email,
                        $log->company_id,
                        $log->company?->name,
                        $log->subject_type,
                        $log->subject_id,
                        $log->ip_address,
                        $log->user_agent,
                        $log->properties ? json_encode($log->properties) : '',
                    ]);
                }
            });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    protected function filteredQuery(Request $request)
    {
        return ActivityLog::query()
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->query('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->query('to')))
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', (int) $request->query('user_id')))
            ->when($request->filled('role'), function ($q) use ($request) {
                $q->whereHas('user.role', fn ($r) => $r->where('slug', $request->query('role')));
            })
            ->when($request->filled('company_id'), fn ($q) => $q->where('company_id', (int) $request->query('company_id')))
            ->when($request->filled('action'), fn ($q) => $q->where('action', $request->query('action')))
            ->when($request->filled('ip'), fn ($q) => $q->where('ip_address', 'like', '%'.$request->query('ip').'%'))
            ->when($request->filled('subject_type'), function ($q) use ($request) {
                $type = $request->query('subject_type');
                if (str_contains($type, '\\')) {
                    $q->where('subject_type', $type);
                } else {
                    $q->where('subject_type', 'like', '%\\'.$type);
                }
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->query('search');
                $q->where(function ($inner) use ($s) {
                    $inner->where('action', 'like', "%{$s}%")
                        ->orWhere('description', 'like', "%{$s}%")
                        ->orWhere('ip_address', 'like', "%{$s}%");
                });
            });
    }

    protected function filterMeta(): array
    {
        return [
            'roles' => Role::orderBy('name')->get(),
            'companies' => Company::orderBy('name')->get(['id', 'name']),
            'users' => User::orderBy('name')->limit(300)->get(['id', 'name', 'email']),
            'actions' => ActivityLog::query()
                ->select('action')
                ->distinct()
                ->orderBy('action')
                ->pluck('action'),
            'subject_types' => ActivityLog::query()
                ->whereNotNull('subject_type')
                ->select('subject_type')
                ->distinct()
                ->orderBy('subject_type')
                ->pluck('subject_type')
                ->mapWithKeys(fn ($type) => [class_basename($type) => $type]),
        ];
    }
}
