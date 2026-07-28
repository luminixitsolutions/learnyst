<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\LandingPage;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LandingPageController extends Controller
{
    use ScopesToCurrentUser;

    public function index()
    {
        $pages = $this->owned(LandingPage::query())->latest()->paginate(20);

        return view('admin.landing-pages.index', compact('pages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'headline' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'cta_text' => ['nullable', 'string', 'max:80'],
            'cta_url' => ['nullable', 'string', 'max:255'],
            'is_published' => ['boolean'],
        ]);

        $validated['created_by'] = Auth::id();
        $validated['slug'] = Str::slug($validated['title']).'-'.Str::lower(Str::random(4));
        $validated['is_published'] = $request->boolean('is_published');
        $validated['cta_text'] = $validated['cta_text'] ?? 'Get started';

        $page = LandingPage::create($validated);
        ActivityLogger::log('landing_page_created', "Landing page {$page->title} created", $page);

        return back()->with('success', 'Landing page created. Public URL: /lp/'.$page->slug);
    }

    public function destroy(LandingPage $landingPage)
    {
        $this->authorizeOwner($landingPage);
        $landingPage->delete();

        return back()->with('success', 'Landing page deleted.');
    }

    public function toggle(LandingPage $landingPage)
    {
        $this->authorizeOwner($landingPage);
        $landingPage->update(['is_published' => ! $landingPage->is_published]);

        return back()->with('success', 'Publish status updated.');
    }
}
