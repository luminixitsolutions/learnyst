<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Webinar;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class WebinarController extends Controller
{
    public function index(Request $request)
    {
        $query = Webinar::with('creator');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sort = $request->get('sort', 'published_date');
        $query = match ($sort) {
            'title' => $query->orderBy('title'),
            'price' => $query->orderBy('price'),
            default => $query->latest(),
        };

        $webinars = $query->paginate(15)->withQueryString();

        return view('admin.webinars.index', compact('webinars', 'sort'));
    }

    public function create()
    {
        return view('admin.webinars.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:60'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'is_free' => ['sometimes', 'boolean'],
            'content_security' => ['required', Rule::in(['encryption', 'no_encryption'])],
        ]);

        $isFree = $request->boolean('is_free');

        if ($isFree) {
            $validated['price'] = 0;
            $validated['is_free'] = true;
        } else {
            $validated['is_free'] = false;
            $request->validate([
                'price' => ['required', 'numeric', 'min:0'],
            ]);
            $validated['price'] = $request->input('price');
        }

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'draft';

        $webinar = Webinar::create($validated);

        ActivityLogger::log('webinar_created', "Webinar {$webinar->title} created", $webinar);

        return redirect()
            ->route('admin.webinars.index')
            ->with('success', 'Webinar created successfully.');
    }

    public function destroy(Webinar $webinar)
    {
        $title = $webinar->title;
        $webinar->delete();

        ActivityLogger::log('webinar_deleted', "Webinar {$title} deleted");

        return redirect()
            ->route('admin.webinars.index')
            ->with('success', 'Webinar deleted.');
    }
}
