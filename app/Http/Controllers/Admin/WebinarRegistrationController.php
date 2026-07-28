<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Webinar;
use App\Models\WebinarRegistration;
use Illuminate\Http\Request;

class WebinarRegistrationController extends Controller
{
    use ScopesToCurrentUser;

    public function index(Request $request)
    {
        $webinarIds = $this->owned(Webinar::query())->pluck('id');

        $query = WebinarRegistration::with('webinar')
            ->whereIn('webinar_id', $webinarIds);

        if ($request->filled('webinar_id')) {
            $query->where('webinar_id', $request->webinar_id);
        }

        $registrations = $query->latest()->limit(500)->get();
        $webinars = $this->owned(Webinar::query())->orderBy('title')->get(['id', 'title']);

        return view('admin.webinar-registrations.index', compact('registrations', 'webinars'));
    }
}
