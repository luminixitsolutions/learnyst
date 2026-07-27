<?php

namespace App\Http\Controllers\Alumni;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $certificates = Certificate::with('course')
            ->where('user_id', Auth::id())
            ->latest('issued_at')
            ->limit(5)
            ->get();

        return view('alumni.dashboard', compact('certificates'));
    }

    public function certificates()
    {
        $certificates = Certificate::with('course')
            ->where('user_id', Auth::id())
            ->latest('issued_at')
            ->paginate(15);

        return view('alumni.certificates', compact('certificates'));
    }
}
