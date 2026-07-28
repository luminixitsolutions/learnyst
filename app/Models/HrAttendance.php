<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrAttendance extends Model
{
    protected $fillable = [
        'employee_id', 'created_by', 'work_date', 'status',
        'check_in', 'check_out', 'notes',
    ];

    protected function casts(): array
    {
        return ['work_date' => 'date'];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
