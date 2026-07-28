<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lead;
use App\Models\LeadCallLog;
use App\Models\LeadFollowUp;
use App\Models\LeadMessage;
use App\Models\LeadNote;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\LeadConversionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CrmController extends Controller
{
    use ScopesToCurrentUser;

    public function __construct(protected LeadConversionService $conversions) {}

    public function pipeline()
    {
        $stages = Lead::stages();
        $all = $this->crmLeadsQuery()->with(['assignee', 'course'])->latest()->get();
        $grouped = collect($stages)->mapWithKeys(fn ($label, $key) => [
            $key => $all->where('stage', $key)->values(),
        ]);

        return view('admin.crm.pipeline', [
            'stages' => $stages,
            'grouped' => $grouped,
        ]);
    }

    public function leads(Request $request)
    {
        $query = $this->crmLeadsQuery()->with(['assignee', 'course', 'convertedUser']);

        if ($request->filled('stage')) {
            $query->where('stage', $request->stage);
        }
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%")
                ->orWhere('phone', 'like', "%{$s}%"));
        }

        $leads = $query->latest()->paginate(20)->withQueryString();
        $counselors = $this->counselorsQuery()->orderBy('name')->get();
        $stages = Lead::stages();

        return view('admin.crm.leads', compact('leads', 'counselors', 'stages'));
    }

    public function show(Lead $lead)
    {
        $this->authorizeCrmLead($lead);
        $lead->load([
            'course', 'assignee', 'convertedUser',
            'followUps.assignee', 'callLogs.user', 'leadNotes.user', 'messages.user',
        ]);
        $counselors = $this->counselorsQuery()->orderBy('name')->get();
        $courses = $this->owned(Course::query())->orderBy('title')->get(['id', 'title']);
        $stages = Lead::stages();

        return view('admin.crm.show', compact('lead', 'counselors', 'courses', 'stages'));
    }

    public function updateStage(Request $request, Lead $lead)
    {
        $this->authorizeCrmLead($lead);
        $validated = $request->validate([
            'stage' => ['required', Rule::in(array_keys(Lead::stages()))],
        ]);

        $lead->update([
            'stage' => $validated['stage'],
            'status' => match ($validated['stage']) {
                'admitted' => 'converted',
                'lost' => 'lost',
                'contacted', 'counseling', 'documents' => 'contacted',
                default => $lead->status === 'converted' ? 'converted' : 'new',
            },
        ]);

        ActivityLogger::log('lead_stage_updated', "Lead {$lead->name} → {$validated['stage']}", $lead);

        return back()->with('success', 'Pipeline stage updated.');
    }

    public function assign(Request $request, Lead $lead)
    {
        $this->authorizeCrmLead($lead);
        $validated = $request->validate([
            'assigned_to' => ['required', Rule::in($this->counselorsQuery()->pluck('id')->all())],
        ]);

        $lead->update([
            'assigned_to' => $validated['assigned_to'],
            'stage' => $lead->stage === 'new' ? 'contacted' : $lead->stage,
        ]);

        return back()->with('success', 'Lead assigned.');
    }

    public function convert(Request $request, Lead $lead)
    {
        $this->authorizeCrmLead($lead);
        $validated = $request->validate([
            'course_id' => ['nullable', Rule::in($this->ownedCourseIds()->all())],
        ]);

        $user = $this->conversions->convert($lead, $validated['course_id'] ?? null, Auth::user());
        $lead->update(['stage' => 'admitted']);

        return back()->with('success', "Admitted as learner {$user->email}.");
    }

    public function storeNote(Request $request, Lead $lead)
    {
        $this->authorizeCrmLead($lead);
        $validated = $request->validate(['body' => ['required', 'string', 'max:5000']]);

        LeadNote::create([
            'lead_id' => $lead->id,
            'user_id' => Auth::id(),
            'body' => $validated['body'],
        ]);

        return back()->with('success', 'Note added.');
    }

    public function storeFollowUp(Request $request, Lead $lead)
    {
        $this->authorizeCrmLead($lead);
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'notes' => ['nullable', 'string'],
            'due_at' => ['nullable', 'date'],
            'assigned_to' => ['nullable', Rule::in($this->counselorsQuery()->pluck('id')->all())],
        ]);

        LeadFollowUp::create([
            'lead_id' => $lead->id,
            'title' => $validated['title'],
            'notes' => $validated['notes'] ?? null,
            'due_at' => $validated['due_at'] ?? null,
            'assigned_to' => $validated['assigned_to'] ?? $lead->assigned_to ?? Auth::id(),
            'created_by' => Auth::id(),
            'status' => 'pending',
        ]);

        return back()->with('success', 'Follow-up scheduled.');
    }

    public function completeFollowUp(LeadFollowUp $followUp)
    {
        $this->authorizeCrmLead($followUp->lead);
        $followUp->update(['status' => 'done', 'completed_at' => now()]);

        return back()->with('success', 'Follow-up completed.');
    }

    public function storeCallLog(Request $request, Lead $lead)
    {
        $this->authorizeCrmLead($lead);
        $validated = $request->validate([
            'direction' => ['required', 'in:outbound,inbound'],
            'outcome' => ['required', 'in:connected,no_answer,busy,voicemail,wrong_number,other'],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
            'called_at' => ['nullable', 'date'],
        ]);

        LeadCallLog::create([
            'lead_id' => $lead->id,
            'user_id' => Auth::id(),
            'direction' => $validated['direction'],
            'outcome' => $validated['outcome'],
            'duration_seconds' => $validated['duration_seconds'] ?? 0,
            'notes' => $validated['notes'] ?? null,
            'called_at' => $validated['called_at'] ?? now(),
        ]);

        if ($lead->stage === 'new') {
            $lead->update(['stage' => 'contacted', 'status' => 'contacted']);
        }

        return back()->with('success', 'Call log saved.');
    }

    public function storeMessage(Request $request, Lead $lead)
    {
        $this->authorizeCrmLead($lead);
        $validated = $request->validate([
            'channel' => ['required', 'in:email,whatsapp,sms'],
            'subject' => ['nullable', 'string', 'max:180'],
            'body' => ['required', 'string', 'max:5000'],
        ]);

        LeadMessage::create([
            'lead_id' => $lead->id,
            'user_id' => Auth::id(),
            'channel' => $validated['channel'],
            'direction' => 'outbound',
            'subject' => $validated['subject'] ?? null,
            'body' => $validated['body'],
            'status' => 'stub',
            'sent_at' => now(),
        ]);

        return back()->with('success', 'Message logged (provider stub — wire SMS/WhatsApp in Integrations).');
    }

    public function followUps(Request $request)
    {
        $query = LeadFollowUp::with(['lead', 'assignee'])
            ->whereHas('lead', fn ($q) => $this->constrainCrmLeadQuery($q));

        if ($request->get('scope') === 'today') {
            $query->whereDate('due_at', today())->where('status', 'pending');
        } elseif ($request->get('scope') === 'mine') {
            $query->where('assigned_to', Auth::id())->where('status', 'pending');
        } else {
            $query->where('status', 'pending');
        }

        $followUps = $query->orderBy('due_at')->paginate(25)->withQueryString();

        return view('admin.crm.follow-ups', compact('followUps'));
    }

    public function callLogs()
    {
        $logs = LeadCallLog::with(['lead', 'user'])
            ->whereHas('lead', fn ($q) => $this->constrainCrmLeadQuery($q))
            ->latest('called_at')
            ->paginate(30);

        return view('admin.crm.call-logs', compact('logs'));
    }

    public function counselorDashboard()
    {
        $userId = Auth::id();
        $isCounselorOnly = Auth::user()?->isCounselor();

        $assignedQuery = $this->crmLeadsQuery();
        if ($isCounselorOnly) {
            $assignedQuery->where('assigned_to', $userId);
        }

        $stats = [
            'assigned' => (clone $assignedQuery)->when($isCounselorOnly, fn ($q) => $q)->count(),
            'today_followups' => LeadFollowUp::where('status', 'pending')
                ->whereDate('due_at', today())
                ->when($isCounselorOnly, fn ($q) => $q->where('assigned_to', $userId))
                ->whereHas('lead', fn ($q) => $this->constrainCrmLeadQuery($q))
                ->count(),
            'converted_month' => (clone $assignedQuery)
                ->where('stage', 'admitted')
                ->whereMonth('converted_at', now()->month)
                ->whereYear('converted_at', now()->year)
                ->count(),
            'calls_week' => LeadCallLog::where('user_id', $userId)
                ->where('called_at', '>=', now()->startOfWeek())
                ->count(),
        ];

        $myLeads = (clone $assignedQuery)
            ->when($isCounselorOnly, fn ($q) => $q->where('assigned_to', $userId))
            ->when(! $isCounselorOnly, fn ($q) => $q->whereNotNull('assigned_to'))
            ->with('course')
            ->latest()
            ->limit(10)
            ->get();

        $todayFollowUps = LeadFollowUp::with('lead')
            ->where('status', 'pending')
            ->whereDate('due_at', today())
            ->when($isCounselorOnly, fn ($q) => $q->where('assigned_to', $userId))
            ->whereHas('lead', fn ($q) => $this->constrainCrmLeadQuery($q))
            ->orderBy('due_at')
            ->get();

        return view('admin.crm.counselor-dashboard', compact('stats', 'myLeads', 'todayFollowUps'));
    }

    public function analytics()
    {
        $base = $this->crmLeadsQuery();

        $bySource = (clone $base)
            ->select('source', DB::raw('count(*) as total'))
            ->groupBy('source')
            ->orderByDesc('total')
            ->get();

        $byStage = (clone $base)
            ->select('stage', DB::raw('count(*) as total'))
            ->groupBy('stage')
            ->get()
            ->keyBy('stage');

        $counselorPerf = (clone $base)
            ->select('assigned_to', DB::raw('count(*) as total'), DB::raw("sum(case when stage = 'admitted' then 1 else 0 end) as converted"))
            ->whereNotNull('assigned_to')
            ->groupBy('assigned_to')
            ->get();

        // load counselors manually
        $users = User::whereIn('id', $counselorPerf->pluck('assigned_to'))->get()->keyBy('id');
        $counselorPerf = $counselorPerf->map(function ($row) use ($users) {
            $row->counselor = $users->get($row->assigned_to);

            return $row;
        });

        $stages = Lead::stages();

        return view('admin.crm.analytics', compact('bySource', 'byStage', 'counselorPerf', 'stages'));
    }

    protected function crmLeadsQuery()
    {
        $user = Auth::user();
        $userId = Auth::id();
        $courseIds = $this->ownedCourseIds();

        $query = Lead::query()->where(function ($q) use ($userId, $courseIds) {
            $q->where('created_by', $userId)
                ->orWhereIn('course_id', $courseIds);
        });

        if ($user?->isCounselor()) {
            $query->where('assigned_to', $userId);
        }

        return $query;
    }

    protected function constrainCrmLeadQuery($q)
    {
        $user = Auth::user();
        $userId = Auth::id();
        $courseIds = $this->ownedCourseIds();

        $q->where(function ($inner) use ($userId, $courseIds) {
            $inner->where('created_by', $userId)
                ->orWhereIn('course_id', $courseIds);
        });

        if ($user?->isCounselor()) {
            $q->where('assigned_to', $userId);
        }

        return $q;
    }

    protected function authorizeCrmLead(Lead $lead): void
    {
        $user = Auth::user();
        $userId = (int) Auth::id();

        if ($user?->isCounselor() && (int) $lead->assigned_to !== $userId) {
            abort(403);
        }

        if ((int) $lead->created_by === $userId) {
            return;
        }
        if ($lead->course_id && $this->ownedCourseIds()->contains($lead->course_id)) {
            return;
        }
        if ($user?->isCounselor() && (int) $lead->assigned_to === $userId) {
            return;
        }

        abort(403);
    }

    protected function counselorsQuery()
    {
        return User::query()
            ->whereHas('role', fn ($q) => $q->whereIn('slug', ['counselor', 'admin', 'sub-admin']))
            ->where(function ($q) {
                $q->where('id', Auth::id())
                    ->orWhere('created_by', Auth::id());
            });
    }
}
