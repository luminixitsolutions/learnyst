<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Company;
use App\Services\CompanyService;
use Illuminate\Support\Facades\Auth;

trait ResolvesCompanyProfile
{
    protected function currentCompany(): Company
    {
        return CompanyService::resolveForUser(Auth::user());
    }

    protected function authorizeCompanyOwned(Company $company, int $companyId): void
    {
        abort_unless((int) $company->id === (int) $companyId, 403);
    }
}
