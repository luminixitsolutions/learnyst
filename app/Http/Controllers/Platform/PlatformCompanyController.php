<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\CompanyService;

class PlatformCompanyController extends Controller
{
    public function index()
    {
        CompanyService::syncMissingCompanies();

        $companies = Company::query()
            ->with('owner')
            ->withCount(['publishedCourses as courses_count'])
            ->latest()
            ->paginate(20);

        return view('platform.companies.index', compact('companies'));
    }
}
