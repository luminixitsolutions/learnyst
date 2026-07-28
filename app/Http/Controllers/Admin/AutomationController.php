<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\AutomationRun;
use App\Models\AutomationWorkflow;
use App\Models\Segment;
use App\Services\ActivityLogger;
use App\Services\AutomationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutomationController extends Controller
{
    use ScopesToCurrentUser;

    public function __construct(protected AutomationService $automations) {}

    public function index()
    {
        $workflows = $this->owned(AutomationWorkflow::query())->latest()->get();
        $triggers = AutomationWorkflow::triggers();

        return view('admin.automations.index', compact('workflows', 'triggers'));
    }

    public function create()
    {
        return view('admin.automations.create', [
            'triggers' => AutomationWorkflow::triggers(),
            'actionTypes' => AutomationWorkflow::actionTypes(),
            'segments' => Segment::where('is_active', true)->orderBy('title')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'trigger_key' => ['required', 'in:'.implode(',', array_keys(AutomationWorkflow::triggers()))],
            'is_active' => ['boolean'],
            'action_type' => ['required', 'in:'.implode(',', array_keys(AutomationWorkflow::actionTypes()))],
            'email_subject' => ['nullable', 'string', 'max:180'],
            'message' => ['nullable', 'string'],
            'follow_up_title' => ['nullable', 'string', 'max:180'],
            'due_hours' => ['nullable', 'integer', 'min:1'],
            'segment_id' => ['nullable', 'exists:segments,id'],
            'coupon_code' => ['nullable', 'string', 'max:60'],
        ]);

        $action = match ($validated['action_type']) {
            'send_email' => [
                'type' => 'send_email',
                'subject' => $validated['email_subject'] ?? 'Hello {{name}}',
                'message' => $validated['message'] ?? 'Hi {{name}}',
            ],
            'send_sms', 'send_whatsapp' => [
                'type' => $validated['action_type'],
                'message' => $validated['message'] ?? 'Hi {{name}}',
            ],
            'create_follow_up' => [
                'type' => 'create_follow_up',
                'title' => $validated['follow_up_title'] ?? 'Follow up',
                'due_hours' => $validated['due_hours'] ?? 24,
                'notes' => $validated['message'] ?? null,
            ],
            'add_segment' => [
                'type' => 'add_segment',
                'segment_id' => $validated['segment_id'] ?? null,
            ],
            'award_coupon' => [
                'type' => 'award_coupon',
                'coupon_code' => $validated['coupon_code'] ?? null,
            ],
            default => ['type' => $validated['action_type']],
        };

        $workflow = AutomationWorkflow::create([
            'created_by' => Auth::id(),
            'name' => $validated['name'],
            'trigger_key' => $validated['trigger_key'],
            'actions' => [$action],
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLogger::log('automation_created', "Automation {$workflow->name} created", $workflow);

        return redirect()->route('admin.automations.index')->with('success', 'Automation created.');
    }

    public function destroy(AutomationWorkflow $automation)
    {
        $this->authorizeOwner($automation);
        $automation->delete();

        return back()->with('success', 'Automation deleted.');
    }

    public function runs(AutomationWorkflow $automation)
    {
        $this->authorizeOwner($automation);
        $runs = AutomationRun::where('automation_workflow_id', $automation->id)->latest()->paginate(30);

        return view('admin.automations.runs', compact('automation', 'runs'));
    }

    public function test(AutomationWorkflow $automation)
    {
        $this->authorizeOwner($automation);
        $this->automations->run($automation, Auth::user(), [
            'email' => Auth::user()->email,
            'phone' => Auth::user()->phone,
            'name' => Auth::user()->name,
        ]);

        return back()->with('success', 'Test run executed. Check run logs.');
    }
}
