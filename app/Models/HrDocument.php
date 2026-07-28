<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HrDocument extends Model
{
    protected $fillable = [
        'employee_id', 'created_by', 'title', 'document_type', 'file_path',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function fileUrl(): ?string
    {
        return $this->file_path ? Storage::disk('public')->url($this->file_path) : null;
    }
}
