<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Services\CertificateLifecycleService;
use Illuminate\Http\Request;

class PublicCertificateController extends Controller
{
    public function verify(Request $request, CertificateLifecycleService $lifecycle)
    {
        $certificate = null;

        if ($request->filled('number')) {
            $certificate = Certificate::with(['user', 'course', 'template'])
                ->where('certificate_number', $request->number)
                ->first();

            if ($certificate) {
                $certificate->status = $lifecycle->resolveStatus($certificate);
            }
        }

        return view('certificates.verify', compact('certificate'));
    }
}
