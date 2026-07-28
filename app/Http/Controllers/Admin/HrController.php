<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\HrAttendance;
use App\Models\HrDocument;
use App\Models\LeaveRequest;
use App\Models\PayrollRun;
use App\Models\SalarySlip;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\PayrollService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class HrController extends Controller
{
    use ScopesToCurrentUser;

    public function __construct(protected PayrollService $payroll) {}

    public function employees()
    {
        $employees = $this->owned(Employee::query())->with('user')->latest()->get();
        $staff = User::whereIn('id', $this->ownedUsersQuery()->pluck('id'))
            ->orWhere('id', Auth::id())
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.hr.employees', compact('employees', 'staff'));
    }

    public function storeEmployee(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:40'],
            'employee_code' => ['nullable', 'string', 'max:40'],
            'department' => ['nullable', 'string', 'max:80'],
            'designation' => ['nullable', 'string', 'max:80'],
            'joined_on' => ['nullable', 'date'],
            'user_id' => ['nullable', 'integer'],
            'basic_salary' => ['nullable', 'numeric', 'min:0'],
            'hra' => ['nullable', 'numeric', 'min:0'],
            'allowances' => ['nullable', 'numeric', 'min:0'],
            'deductions' => ['nullable', 'numeric', 'min:0'],
        ]);

        if (! empty($validated['user_id'])) {
            abort_unless(
                (int) $validated['user_id'] === Auth::id()
                || $this->ownedUsersQuery()->whereKey($validated['user_id'])->exists(),
                403
            );
        }

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'active';
        $employee = Employee::create($validated);
        ActivityLogger::log('employee_created', "Employee {$employee->name} created", $employee);

        return back()->with('success', 'Employee added.');
    }

    public function showEmployee(Employee $employee)
    {
        $this->authorizeOwner($employee);
        $employee->load(['documents', 'leaveRequests' => fn ($q) => $q->latest()->limit(10)]);

        return view('admin.hr.employee-show', compact('employee'));
    }

    public function attendance(Request $request)
    {
        $employees = $this->owned(Employee::query())->where('status', 'active')->orderBy('name')->get();
        $date = $request->get('date', now()->toDateString());
        $rows = HrAttendance::whereIn('employee_id', $employees->pluck('id'))
            ->whereDate('work_date', $date)
            ->get()
            ->keyBy('employee_id');
        $isToday = $date === now()->toDateString();

        return view('admin.hr.attendance', compact('employees', 'date', 'rows', 'isToday'));
    }

    public function storeAttendance(Request $request)
    {
        $validated = $request->validate([
            'work_date' => ['required', 'date'],
            'entries' => ['required', 'array'],
            'entries.*.employee_id' => ['required', 'integer'],
            'entries.*.status' => ['required', 'in:present,absent,half_day,leave,holiday'],
            'entries.*.check_in' => ['nullable', 'date_format:H:i'],
            'entries.*.check_out' => ['nullable', 'date_format:H:i'],
            'entries.*.notes' => ['nullable', 'string', 'max:500'],
        ]);

        foreach ($validated['entries'] as $row) {
            $employee = $this->owned(Employee::query())->whereKey($row['employee_id'])->first();
            if (! $employee) {
                continue;
            }

            $payload = [
                'created_by' => Auth::id(),
                'status' => $row['status'],
                'notes' => $row['notes'] ?? null,
            ];

            if (! empty($row['check_in'])) {
                $payload['check_in'] = $row['check_in'].':00';
            }
            if (! empty($row['check_out'])) {
                $payload['check_out'] = $row['check_out'].':00';
            }

            HrAttendance::updateOrCreate(
                ['employee_id' => $employee->id, 'work_date' => $validated['work_date']],
                $payload
            );
        }

        ActivityLogger::log('hr_attendance_saved', 'Attendance saved for '.$validated['work_date']);

        return back()->with('success', 'Attendance saved.');
    }

    public function punchIn(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'integer'],
            'work_date' => ['required', 'date'],
        ]);

        $employee = $this->owned(Employee::query())
            ->whereKey($validated['employee_id'])
            ->where('status', 'active')
            ->firstOrFail();

        $existing = HrAttendance::where('employee_id', $employee->id)
            ->whereDate('work_date', $validated['work_date'])
            ->first();

        if ($existing?->check_in) {
            return back()->with('error', "{$employee->name} already punched in at ".substr((string) $existing->check_in, 0, 5).'.');
        }

        $now = now()->format('H:i:s');
        HrAttendance::updateOrCreate(
            ['employee_id' => $employee->id, 'work_date' => $validated['work_date']],
            [
                'created_by' => Auth::id(),
                'status' => 'present',
                'check_in' => $now,
            ]
        );

        ActivityLogger::log('hr_punch_in', "{$employee->name} punched in at {$now}", $employee);

        return back()->with('success', "{$employee->name} punched in at ".substr($now, 0, 5).'.');
    }

    public function punchOut(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => ['required', 'integer'],
            'work_date' => ['required', 'date'],
        ]);

        $employee = $this->owned(Employee::query())
            ->whereKey($validated['employee_id'])
            ->where('status', 'active')
            ->firstOrFail();

        $existing = HrAttendance::where('employee_id', $employee->id)
            ->whereDate('work_date', $validated['work_date'])
            ->first();

        if (! $existing?->check_in) {
            return back()->with('error', "{$employee->name} must punch in before punch out.");
        }

        if ($existing->check_out) {
            return back()->with('error', "{$employee->name} already punched out at ".substr((string) $existing->check_out, 0, 5).'.');
        }

        $now = now()->format('H:i:s');
        if ($now < (string) $existing->check_in) {
            return back()->with('error', 'Punch out time cannot be earlier than punch in.');
        }

        $existing->update([
            'check_out' => $now,
            'status' => $existing->status === 'absent' ? 'present' : $existing->status,
            'created_by' => Auth::id(),
        ]);

        ActivityLogger::log('hr_punch_out', "{$employee->name} punched out at {$now}", $employee);

        return back()->with('success', "{$employee->name} punched out at ".substr($now, 0, 5).'.');
    }

    public function leaves()
    {
        $leaves = LeaveRequest::with('employee')
            ->whereHas('employee', fn ($q) => $q->where('created_by', Auth::id()))
            ->latest()
            ->get();
        $employees = $this->owned(Employee::query())->where('status', 'active')->orderBy('name')->get();

        return view('admin.hr.leaves', compact('leaves', 'employees'));
    }

    public function storeLeave(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => ['required', Rule::in($this->owned(Employee::query())->pluck('id')->all())],
            'leave_type' => ['required', 'string', 'max:40'],
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
            'reason' => ['nullable', 'string'],
        ]);
        $validated['created_by'] = Auth::id();
        $validated['status'] = 'pending';
        LeaveRequest::create($validated);

        return back()->with('success', 'Leave request submitted.');
    }

    public function reviewLeave(Request $request, LeaveRequest $leave)
    {
        abort_unless($leave->employee && (int) $leave->employee->created_by === Auth::id(), 403);
        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'review_notes' => ['nullable', 'string'],
        ]);
        $leave->update([
            'status' => $validated['status'],
            'review_notes' => $validated['review_notes'] ?? null,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Leave '.$validated['status'].'.');
    }

    public function payroll()
    {
        $runs = $this->owned(PayrollRun::query())->withCount('slips')->latest()->get();

        return view('admin.hr.payroll', compact('runs'));
    }

    public function storePayroll(Request $request)
    {
        $validated = $request->validate([
            'year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'notes' => ['nullable', 'string'],
        ]);

        $run = PayrollRun::firstOrCreate(
            [
                'created_by' => Auth::id(),
                'year' => $validated['year'],
                'month' => $validated['month'],
            ],
            ['status' => 'draft', 'notes' => $validated['notes'] ?? null]
        );

        $this->payroll->process($run);

        return redirect()->route('admin.hr.payroll.show', $run)->with('success', 'Payroll processed.');
    }

    public function showPayroll(PayrollRun $payroll)
    {
        $this->authorizeOwner($payroll);
        $payroll->load(['slips.employee']);

        return view('admin.hr.payroll-show', ['run' => $payroll]);
    }

    public function salarySlip(SalarySlip $slip)
    {
        abort_unless((int) $slip->created_by === Auth::id(), 403);
        $slip->load(['employee', 'payrollRun']);

        return view('admin.hr.salary-slip', compact('slip'));
    }

    public function storeDocument(Request $request, Employee $employee)
    {
        $this->authorizeOwner($employee);
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'document_type' => ['nullable', 'string', 'max:60'],
            'file' => ['required', 'file', 'max:5120'],
        ]);

        $path = $request->file('file')->store('hr/documents', 'public');
        HrDocument::create([
            'employee_id' => $employee->id,
            'created_by' => Auth::id(),
            'title' => $validated['title'],
            'document_type' => $validated['document_type'] ?? null,
            'file_path' => $path,
        ]);

        return back()->with('success', 'Document uploaded.');
    }
}
