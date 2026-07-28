<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\CompanyBrandingService;
use Illuminate\Http\Request;

class PlatformBrandingController extends Controller
{
    public function __construct(protected CompanyBrandingService $branding) {}

    public function index(Request $request)
    {
        $query = Company::with('owner')->latest();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('custom_domain', 'like', '%'.$request->search.'%');
        }

        $companies = $query->paginate(30)->withQueryString();

        return view('platform.branding.index', compact('companies'));
    }

    public function show(Company $company)
    {
        $dns = $this->branding->dnsInstructions($company);

        return view('platform.branding.show', compact('company', 'dns'));
    }

    public function verify(Company $company)
    {
        $this->branding->markVerified($company);

        return back()->with('success', 'Company domain marked verified.');
    }
}
