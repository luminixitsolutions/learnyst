<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\CourseEnrollment;
use App\Models\LibraryItem;
use App\Models\LearnerSubscription;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LibraryController extends Controller
{
    public function __construct(protected GamificationService $gamification) {}

    public function index(Request $request)
    {
        $instituteId = $this->gamification->resolveInstituteId(Auth::user());
        $query = LibraryItem::query()
            ->where('status', 'published')
            ->when($instituteId, fn ($q) => $q->where('created_by', $instituteId));

        if ($request->filled('type')) {
            $query->where('item_type', $request->type);
        }

        $items = $query->latest()->paginate(18)->withQueryString();
        $types = LibraryItem::types();

        return view('learner.library.index', compact('items', 'types'));
    }

    public function show(LibraryItem $item)
    {
        abort_unless($item->status === 'published', 404);
        abort_unless($this->canAccess($item), 403);

        $item->increment('view_count');

        return view('learner.library.show', compact('item'));
    }

    public function read(LibraryItem $item)
    {
        abort_unless($item->status === 'published', 404);
        abort_unless($this->canAccess($item), 403);
        abort_unless($item->fileUrl(), 404);

        $item->increment('view_count');

        return view('learner.library.reader', compact('item'));
    }

    public function download(LibraryItem $item)
    {
        abort_unless($item->status === 'published', 404);
        abort_unless($this->canAccess($item), 403);
        abort_unless($item->allow_download, 403);
        abort_unless($item->file_path && Storage::disk('public')->exists($item->file_path), 404);

        $item->increment('download_count');

        return Storage::disk('public')->download($item->file_path, $item->title.'.'.pathinfo($item->file_path, PATHINFO_EXTENSION));
    }

    protected function canAccess(LibraryItem $item): bool
    {
        return match ($item->access_mode) {
            'public' => true,
            'private' => false,
            'subscription' => LearnerSubscription::where('user_id', Auth::id())
                ->whereIn('status', ['active', 'trialing'])
                ->exists(),
            'enrolled' => $item->course_id
                ? CourseEnrollment::where('user_id', Auth::id())
                    ->where('course_id', $item->course_id)
                    ->where('status', 'active')
                    ->exists()
                : CourseEnrollment::where('user_id', Auth::id())->where('status', 'active')->exists(),
            default => false,
        };
    }
}
