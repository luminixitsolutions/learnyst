<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollRun extends Model
{
    protected $fillable = [
        'created_by', 'year', 'month', 'status', 'processed_at', 'notes',
    ];

    protected function casts(): array
    {
        return ['processed_at' => 'datetime'];
    }

    public function slips()
    {
        return $this->hasMany(SalarySlip::class);
    }

    public function periodLabel(): string
    {
        return sprintf('%04d-%02d', $this->year, $this->month);
    }
}
