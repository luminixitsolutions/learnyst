<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\WebsiteContent;
use App\Services\ActivityLogger;
use App\Services\BlogService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlatformBlogController extends Controller
{
    public function edit()
    {
        $meta = BlogService::pageMeta();
        $content = BlogService::getPage();

        return view('platform.blogs.edit', compact('meta', 'content'));
    }

    public function update(Request $request)
    {
        $meta = BlogService::pageMeta();
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:80'],
            'eyebrow' => ['nullable', 'string', 'max:80'],
            'summary' => ['required', 'string', 'max:500'],
            'body' => ['required', 'string', 'max:5000'],
            'hero_gradient' => ['nullable', 'string', 'max:255'],
            'cta_title' => ['nullable', 'string', 'max:255'],
            'cta_text' => ['nullable', 'string', 'max:1000'],
            'cta_primary_label' => ['nullable', 'string', 'max:80'],
            'cta_secondary_label' => ['nullable', 'string', 'max:80'],
            'posts' => ['nullable', 'array'],
            'posts.*.slug' => ['nullable', 'string', 'max:160'],
            'posts.*.title' => ['nullable', 'string', 'max:255'],
            'posts.*.excerpt' => ['nullable', 'string', 'max:500'],
            'posts.*.body' => ['nullable', 'string', 'max:20000'],
            'posts.*.tag' => ['nullable', 'string', 'max:80'],
            'posts.*.date' => ['nullable', 'string', 'max:80'],
            'posts.*.read' => ['nullable', 'string', 'max:40'],
            'posts.*.author' => ['nullable', 'string', 'max:120'],
            'posts.*.featured' => ['nullable'],
            'posts.*.is_active' => ['nullable'],
        ]);

        $posts = [];
        $usedSlugs = [];

        foreach ($validated['posts'] ?? [] as $row) {
            $title = trim((string) ($row['title'] ?? ''));
            if ($title === '') {
                continue;
            }

            $slug = trim((string) ($row['slug'] ?? ''));
            if ($slug === '') {
                $slug = Str::slug($title);
            }
            $slug = Str::slug($slug);

            if (isset($usedSlugs[$slug])) {
                $slug .= '-'.substr(md5($title.microtime()), 0, 4);
            }
            $usedSlugs[$slug] = true;

            $posts[] = [
                'slug' => $slug,
                'title' => $title,
                'excerpt' => trim((string) ($row['excerpt'] ?? '')),
                'body' => trim((string) ($row['body'] ?? '')),
                'tag' => trim((string) ($row['tag'] ?? '')),
                'date' => trim((string) ($row['date'] ?? '')),
                'read' => trim((string) ($row['read'] ?? '')),
                'author' => trim((string) ($row['author'] ?? 'StudyNest Team')),
                'featured' => ! empty($row['featured']),
                'is_active' => ! empty($row['is_active']),
            ];
        }

        if (count($posts) < 1) {
            return back()->withErrors(['posts' => 'Add at least one blog post.'])->withInput();
        }

        WebsiteContent::putContent(
            BlogService::contentKey(),
            $meta['label'],
            [
                'title' => $validated['title'],
                'caption' => $validated['caption'] ?? 'Resources',
                'eyebrow' => $validated['eyebrow'] ?? '',
                'summary' => $validated['summary'],
                'body' => $validated['body'],
                'hero_gradient' => $validated['hero_gradient'] ?? '',
                'cta_title' => $validated['cta_title'] ?? '',
                'cta_text' => $validated['cta_text'] ?? '',
                'cta_primary_label' => $validated['cta_primary_label'] ?? 'Start Free Trial',
                'cta_secondary_label' => $validated['cta_secondary_label'] ?? 'Book a Demo',
                'posts' => $posts,
            ],
            'resources',
            $meta['sort']
        );

        ActivityLogger::log('blogs_updated', 'Blog listing and posts updated');

        return redirect()
            ->route('platform.blogs.edit')
            ->with('success', 'Blog pages saved.');
    }

    public function reset()
    {
        $meta = BlogService::pageMeta();
        $key = BlogService::contentKey();
        WebsiteContent::query()->where('key', $key)->delete();
        cache()->forget("website_content.{$key}");

        ActivityLogger::log('blogs_reset', 'Blog pages reset to defaults');

        return redirect()
            ->route('platform.blogs.edit')
            ->with('success', 'Blogs reset to defaults.');
    }
}
