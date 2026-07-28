<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\HrAttendance;
use App\Models\LeaveRequest;
use App\Models\PayrollRun;
use App\Models\SalarySlip;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollService
{
    public function process(PayrollRun $run): PayrollRun
    {
        $employees = Employee::where('created_by', $run->created_by)
            ->where('status', 'active')
            ->get();

        $start = Carbon::create($run->year, $run->month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        return DB::transaction(function () use ($run, $employees, $start, $end) {
            $run->slips()->delete();
            $i = 1;

            foreach ($employees as $employee) {
                $present = HrAttendance::where('employee_id', $employee->id)
                    ->whereBetween('work_date', [$start, $end])
                    ->whereIn('status', ['present', 'half_day'])
                    ->count();

                $leaveDays = LeaveRequest::where('employee_id', $employee->id)
                    ->where('status', 'approved')
                    ->where(function ($q) use ($start, $end) {
                        $q->whereBetween('from_date', [$start, $end])
                            ->orWhereBetween('to_date', [$start, $end]);
                    })
                    ->get()
                    ->sum(fn (LeaveRequest $l) => $l->from_date->diffInDays($l->to_date) + 1);

                $basic = (float) $employee->basic_salary;
                $hra = (float) $employee->hra;
                $allow = (float) $employee->allowances;
                $deduct = (float) $employee->deductions;
                $net = $basic + $hra + $allow - $deduct;

                SalarySlip::create([
                    'payroll_run_id' => $run->id,
                    'employee_id' => $employee->id,
                    'created_by' => $run->created_by,
                    'slip_number' => sprintf('SLIP-%s-%04d', $run->periodLabel(), $i++),
                    'basic_salary' => $basic,
                    'hra' => $hra,
                    'allowances' => $allow,
                    'deductions' => $deduct,
                    'net_pay' => max(0, $net),
                    'present_days' => $present,
                    'leave_days' => (int) $leaveDays,
                    'breakdown' => [
                        'gross' => $basic + $hra + $allow,
                        'net' => max(0, $net),
                    ],
                ]);
            }

            $run->update(['status' => 'processed', 'processed_at' => now()]);

            return $run->fresh('slips');
        });
    }
}
