<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomationRun extends Model
{
    protected $fillable = [
        'automation_workflow_id', 'trigger_key', 'subject_type', 'subject_id',
        'status', 'context', 'result', 'error',
    ];

    protected function casts(): array
    {
        return [
            'context' => 'array',
            'result' => 'array',
        ];
    }

    public function workflow()
    {
        return $this->belongsTo(AutomationWorkflow::class, 'automation_workflow_id');
    }

    public function subject()
    {
        return $this->morphTo();
    }
}
