<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyWebsiteBlock;
use App\Models\CompanyWebsiteMenu;
use App\Models\CompanyWebsitePage;
use App\Services\ActivityLogger;
use App\Services\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class WebsiteBuilderController extends Controller
{
    protected function company()
    {
        return CompanyService::resolveForUser(Auth::user());
    }

    protected function assertPage(CompanyWebsitePage $page): void
    {
        abort_unless((int) $page->company_id === (int) $this->company()->id, 403);
    }

    public function index()
    {
        $company = $this->company();
        $pages = CompanyWebsitePage::where('company_id', $company->id)
            ->withCount('blocks')
            ->orderBy('nav_sort')
            ->orderBy('title')
            ->get();

        return view('admin.website-builder.index', compact('company', 'pages'));
    }

    public function createPage()
    {
        return view('admin.website-builder.page-form', [
            'page' => new CompanyWebsitePage(['status' => 'draft', 'page_type' => 'custom', 'show_in_nav' => true]),
            'mode' => 'create',
        ]);
    }

    public function storePage(Request $request)
    {
        $company = $this->company();
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('company_website_pages', 'slug')->where('company_id', $company->id)],
            'page_type' => ['required', 'in:home,about,contact,faq,testimonials,faculty,gallery,blog,custom'],
            'status' => ['required', 'in:draft,published'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'show_in_nav' => ['nullable', 'boolean'],
        ]);

        $page = CompanyWebsitePage::create([
            'company_id' => $company->id,
            'title' => $validated['title'],
            'slug' => $validated['slug'] ?: Str::slug($validated['title']),
            'page_type' => $validated['page_type'],
            'status' => $validated['status'],
            'seo_title' => $validated['seo_title'] ?? null,
            'seo_description' => $validated['seo_description'] ?? null,
            'show_in_nav' => $request->boolean('show_in_nav', true),
            'nav_sort' => (CompanyWebsitePage::where('company_id', $company->id)->max('nav_sort') ?? 0) + 1,
        ]);

        ActivityLogger::log('website_page_created', "Website page created: {$page->title}", $page, [
            'company_id' => $company->id,
        ]);

        return redirect()->route('admin.website-builder.pages.edit', $page)->with('success', 'Page created. Add blocks below.');
    }

    public function editPage(CompanyWebsitePage $page)
    {
        $this->assertPage($page);
        $page->load(['blocks' => fn ($q) => $q->orderBy('sort_order')]);

        return view('admin.website-builder.page-edit', [
            'page' => $page,
            'blockTypes' => CompanyWebsiteBlock::blockTypes(),
            'company' => $this->company(),
        ]);
    }

    public function updatePage(Request $request, CompanyWebsitePage $page)
    {
        $this->assertPage($page);
        $company = $this->company();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('company_website_pages', 'slug')->where('company_id', $company->id)->ignore($page->id)],
            'page_type' => ['required', 'in:home,about,contact,faq,testimonials,faculty,gallery,blog,custom'],
            'status' => ['required', 'in:draft,published'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'show_in_nav' => ['nullable', 'boolean'],
        ]);

        $page->update([
            ...$validated,
            'show_in_nav' => $request->boolean('show_in_nav'),
        ]);

        ActivityLogger::log('website_page_updated', "Website page updated: {$page->title}", $page);

        return back()->with('success', 'Page saved.');
    }

    public function destroyPage(CompanyWebsitePage $page)
    {
        $this->assertPage($page);
        $title = $page->title;
        $page->delete();
        ActivityLogger::log('website_page_deleted', "Website page deleted: {$title}");

        return redirect()->route('admin.website-builder.index')->with('success', 'Page deleted.');
    }

    public function storeBlock(Request $request, CompanyWebsitePage $page)
    {
        $this->assertPage($page);
        $validated = $request->validate([
            'block_type' => ['required', Rule::in(array_keys(CompanyWebsiteBlock::blockTypes()))],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        $defaults = $this->defaultBlockContent($validated['block_type']);

        $block = CompanyWebsiteBlock::create([
            'company_website_page_id' => $page->id,
            'block_type' => $validated['block_type'],
            'title' => $validated['title'] ?: (CompanyWebsiteBlock::blockTypes()[$validated['block_type']] ?? 'Block'),
            'content' => $defaults,
            'is_enabled' => true,
            'sort_order' => ($page->blocks()->max('sort_order') ?? 0) + 1,
        ]);

        return back()->with('success', 'Block added.')->with('focus_block', $block->id);
    }

    public function updateBlock(Request $request, CompanyWebsiteBlock $block)
    {
        $this->assertPage($block->page);
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'is_enabled' => ['nullable', 'boolean'],
            'headline' => ['nullable', 'string', 'max:255'],
            'subheadline' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'cta_label' => ['nullable', 'string', 'max:120'],
            'cta_url' => ['nullable', 'string', 'max:500'],
            'image_url' => ['nullable', 'string', 'max:500'],
            'items_json' => ['nullable', 'string'],
        ]);

        $content = $block->content ?? [];
        foreach (['headline', 'subheadline', 'body', 'cta_label', 'cta_url', 'image_url'] as $key) {
            if (array_key_exists($key, $validated)) {
                $content[$key] = $validated[$key];
            }
        }
        if ($request->filled('items_json')) {
            $decoded = json_decode($request->items_json, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $content['items'] = $decoded;
            }
        }

        $block->update([
            'title' => $validated['title'] ?? $block->title,
            'is_enabled' => $request->boolean('is_enabled'),
            'content' => $content,
        ]);

        return back()->with('success', 'Block updated.');
    }

    public function destroyBlock(CompanyWebsiteBlock $block)
    {
        $this->assertPage($block->page);
        $block->delete();

        return back()->with('success', 'Block removed.');
    }

    public function reorderBlocks(Request $request, CompanyWebsitePage $page)
    {
        $this->assertPage($page);
        $order = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer'],
        ])['order'];

        foreach ($order as $index => $blockId) {
            CompanyWebsiteBlock::where('id', $blockId)
                ->where('company_website_page_id', $page->id)
                ->update(['sort_order' => $index + 1]);
        }

        return response()->json(['ok' => true]);
    }

    public function menus()
    {
        $company = $this->company();
        $header = CompanyWebsiteMenu::where('company_id', $company->id)->where('location', 'header')->orderBy('sort_order')->get();
        $footer = CompanyWebsiteMenu::where('company_id', $company->id)->where('location', 'footer')->orderBy('sort_order')->get();
        $pages = CompanyWebsitePage::where('company_id', $company->id)->where('status', 'published')->orderBy('title')->get();

        return view('admin.website-builder.menus', compact('company', 'header', 'footer', 'pages'));
    }

    public function storeMenu(Request $request)
    {
        $company = $this->company();
        $validated = $request->validate([
            'location' => ['required', 'in:header,footer'],
            'label' => ['required', 'string', 'max:120'],
            'url' => ['nullable', 'string', 'max:500'],
            'page_id' => ['nullable', 'exists:company_website_pages,id'],
        ]);

        if (! empty($validated['page_id'])) {
            abort_unless(
                CompanyWebsitePage::where('id', $validated['page_id'])->where('company_id', $company->id)->exists(),
                403
            );
        }

        CompanyWebsiteMenu::create([
            'company_id' => $company->id,
            'location' => $validated['location'],
            'label' => $validated['label'],
            'url' => $validated['url'] ?? null,
            'page_id' => $validated['page_id'] ?? null,
            'sort_order' => (CompanyWebsiteMenu::where('company_id', $company->id)->where('location', $validated['location'])->max('sort_order') ?? 0) + 1,
            'is_enabled' => true,
        ]);

        return back()->with('success', 'Menu item added.');
    }

    public function updateMenu(Request $request, CompanyWebsiteMenu $menu)
    {
        abort_unless((int) $menu->company_id === (int) $this->company()->id, 403);
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:120'],
            'url' => ['nullable', 'string', 'max:500'],
            'is_enabled' => ['nullable', 'boolean'],
        ]);
        $menu->update([
            'label' => $validated['label'],
            'url' => $validated['url'] ?? null,
            'is_enabled' => $request->boolean('is_enabled'),
        ]);

        return back()->with('success', 'Menu item updated.');
    }

    public function destroyMenu(CompanyWebsiteMenu $menu)
    {
        abort_unless((int) $menu->company_id === (int) $this->company()->id, 403);
        $menu->delete();

        return back()->with('success', 'Menu item removed.');
    }

    public function reorderMenus(Request $request)
    {
        $company = $this->company();
        $order = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer'],
        ])['order'];

        foreach ($order as $index => $menuId) {
            CompanyWebsiteMenu::where('id', $menuId)
                ->where('company_id', $company->id)
                ->update(['sort_order' => $index + 1]);
        }

        return response()->json(['ok' => true]);
    }

    public function seo()
    {
        $company = $this->company();
        $pages = CompanyWebsitePage::where('company_id', $company->id)->orderBy('title')->get();

        return view('admin.website-builder.seo', compact('company', 'pages'));
    }

    public function updateSeo(Request $request)
    {
        $company = $this->company();
        $data = $request->validate([
            'pages' => ['required', 'array'],
            'pages.*.seo_title' => ['nullable', 'string', 'max:255'],
            'pages.*.seo_description' => ['nullable', 'string', 'max:500'],
        ]);

        foreach ($data['pages'] as $pageId => $seo) {
            CompanyWebsitePage::where('id', $pageId)
                ->where('company_id', $company->id)
                ->update([
                    'seo_title' => $seo['seo_title'] ?? null,
                    'seo_description' => $seo['seo_description'] ?? null,
                ]);
        }

        return back()->with('success', 'SEO meta updated.');
    }

    protected function defaultBlockContent(string $type): array
    {
        return match ($type) {
            'hero' => ['headline' => 'Welcome', 'subheadline' => 'Learn with us', 'cta_label' => 'Browse courses', 'cta_url' => '#courses', 'image_url' => ''],
            'text' => ['headline' => 'About', 'body' => 'Write your content here.'],
            'cta' => ['headline' => 'Ready to start?', 'subheadline' => 'Join today', 'cta_label' => 'Get started', 'cta_url' => '#'],
            'testimonials' => ['headline' => 'What students say', 'items' => [['name' => 'Student', 'quote' => 'Great courses!']]],
            'faculty' => ['headline' => 'Our faculty', 'items' => [['name' => 'Instructor', 'role' => 'Lead mentor']]],
            'faq' => ['headline' => 'FAQ', 'items' => [['q' => 'How do I enroll?', 'a' => 'Browse courses and checkout.']]],
            'gallery' => ['headline' => 'Gallery', 'items' => [['image_url' => '', 'caption' => '']]],
            'form' => ['headline' => 'Contact us', 'body' => 'Send an enquiry and we will reply soon.'],
            'newsletter' => ['headline' => 'Stay updated', 'subheadline' => 'Get course news in your inbox.'],
            'pricing' => ['headline' => 'Pricing', 'items' => [['name' => 'Starter', 'price' => 'Free', 'features' => 'Access basics']]],
            'courses' => ['headline' => 'Featured courses', 'body' => 'Published courses from your catalog appear here.'],
            default => [],
        };
    }
}
