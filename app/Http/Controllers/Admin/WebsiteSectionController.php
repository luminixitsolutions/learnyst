<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSection;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WebsiteSectionController extends Controller
{
    public function index()
    {
        $sections = WebsiteSection::orderBy('sort_order')->get();

        return view('admin.website-sections.index', compact('sections'));
    }

    public function create()
    {
        return view('admin.website-sections.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateSection($request);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('website', 'public');
        }

        $validated['section_key'] = Str::slug($validated['name']) . '-' . Str::random(4);
        $section = WebsiteSection::create($validated);
        ActivityLogger::log('website_section_created', "Section {$section->name} created", $section);

        return redirect()->route('admin.website-sections.index')->with('success', 'Homepage section created.');
    }

    public function edit(WebsiteSection $websiteSection)
    {
        return view('admin.website-sections.edit', compact('websiteSection'));
    }

    public function update(Request $request, WebsiteSection $websiteSection)
    {
        $validated = $this->validateSection($request, $websiteSection->id);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('website', 'public');
        }

        $websiteSection->update($validated);
        ActivityLogger::log('website_section_updated', "Section {$websiteSection->name} updated", $websiteSection);

        return redirect()->route('admin.website-sections.index')->with('success', 'Section updated.');
    }

    public function destroy(WebsiteSection $websiteSection)
    {
        ActivityLogger::log('website_section_deleted', "Section {$websiteSection->name} deleted", $websiteSection);
        $websiteSection->delete();

        return redirect()->route('admin.website-sections.index')->with('success', 'Section deleted.');
    }

    public function preview()
    {
        return redirect()->route('home');
    }

    protected function validateSection(Request $request, ?int $ignoreId = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'heading' => ['nullable', 'string', 'max:255'],
            'sub_heading' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'button_link' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_visible' => ['boolean'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);

        $validated['is_visible'] = $request->boolean('is_visible', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        return $validated;
    }
}
