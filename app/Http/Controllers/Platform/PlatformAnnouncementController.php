<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\PlatformAnnouncement;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlatformAnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = PlatformAnnouncement::query()
            ->with(['company', 'creator'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('audience')) {
            $query->where('audience', $request->audience);
        }
        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%");
            });
        }

        $stats = [
            'total' => PlatformAnnouncement::count(),
            'published' => PlatformAnnouncement::where('status', 'published')->count(),
            'scheduled' => PlatformAnnouncement::where('status', 'scheduled')->count(),
            'draft' => PlatformAnnouncement::where('status', 'draft')->count(),
        ];

        $announcements = $query->paginate(20)->withQueryString();

        return view('platform.announcements.index', compact('announcements', 'stats'));
    }

    public function create()
    {
        $companies = Company::orderBy('name')->get(['id', 'name']);
        $announcement = new PlatformAnnouncement([
            'audience' => 'all_institutes',
            'status' => 'draft',
        ]);

        return view('platform.announcements.form', [
            'announcement' => $announcement,
            'companies' => $companies,
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $validated['created_by'] = $request->user()->id;

        $announcement = PlatformAnnouncement::create($validated);

        ActivityLogger::log('announcement_created', "Announcement created: {$announcement->title}", $announcement);

        return redirect()
            ->route('platform.announcements.index')
            ->with('success', 'Announcement created.');
    }

    public function edit(PlatformAnnouncement $announcement)
    {
        $companies = Company::orderBy('name')->get(['id', 'name']);

        return view('platform.announcements.form', [
            'announcement' => $announcement,
            'companies' => $companies,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, PlatformAnnouncement $announcement)
    {
        $announcement->update($this->validated($request));

        ActivityLogger::log('announcement_updated', "Announcement updated: {$announcement->title}", $announcement);

        return redirect()
            ->route('platform.announcements.index')
            ->with('success', 'Announcement updated.');
    }

    public function destroy(PlatformAnnouncement $announcement)
    {
        $title = $announcement->title;
        $announcement->delete();

        ActivityLogger::log('announcement_deleted', "Announcement deleted: {$title}");

        return redirect()
            ->route('platform.announcements.index')
            ->with('success', 'Announcement deleted.');
    }

    protected function validated(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:10000'],
            'audience' => ['required', Rule::in(['all_institutes', 'institute_admins', 'specific'])],
            'company_id' => ['nullable', 'required_if:audience,specific', 'exists:companies,id'],
            'status' => ['required', Rule::in(['draft', 'scheduled', 'published', 'archived'])],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        if (($validated['audience'] ?? '') !== 'specific') {
            $validated['company_id'] = null;
        }

        return $validated;
    }
}
