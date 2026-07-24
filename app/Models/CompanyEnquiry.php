<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyEnquiry extends Model
{
    protected $fillable = [
        'company_id', 'name', 'email', 'phone', 'subject', 'message', 'status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
