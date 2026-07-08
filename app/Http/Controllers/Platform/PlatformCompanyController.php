<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;

class PlatformCompanyController extends Controller
{
    public function index()
    {
        $company = [
            'name' => Setting::get('site_name', 'Learnyst Academy'),
            'email' => Setting::get('contact_email', 'admin@learnyst.com'),
            'status' => 'Active',
            'users' => User::whereHas('role', fn ($q) => $q->whereIn('slug', ['admin', 'sub-admin', 'instructor', 'learner']))->count(),
            'created_at' => Setting::get('company_created_at', now()->format('Y-m-d')),
        ];

        return view('platform.companies.index', compact('company'));
    }
}
