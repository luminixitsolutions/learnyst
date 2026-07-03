<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCopyLog extends Model
{
    protected $fillable = [
        'source_title',
        'product_type',
        'destination_title',
        'status',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function typeLabel(): string
    {
        return match ($this->product_type) {
            'mock-test' => 'Mock Test',
            'test-series' => 'Test Series',
            default => 'Course',
        };
    }

    public function statusLabel(): string
    {
        return ucfirst($this->status);
    }
}
