<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'created_by', 'user_id', 'employee_code', 'name', 'email', 'phone',
        'department', 'designation', 'joined_on', 'status',
        'basic_salary', 'hra', 'allowances', 'deductions', 'meta',
    ];

    protected function casts(): array
    {
        return [
            'joined_on' => 'date',
            'basic_salary' => 'decimal:2',
            'hra' => 'decimal:2',
            'allowances' => 'decimal:2',
            'deductions' => 'decimal:2',
            'meta' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendances()
    {
        return $this->hasMany(HrAttendance::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function documents()
    {
        return $this->hasMany(HrDocument::class);
    }

    public function grossSalary(): float
    {
        return (float) $this->basic_salary + (float) $this->hra + (float) $this->allowances;
    }

    public function netSalary(): float
    {
        return $this->grossSalary() - (float) $this->deductions;
    }
}
