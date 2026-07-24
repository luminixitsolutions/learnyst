<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\WebsiteContent;
use App\Services\ActivityLogger;
use App\Services\WebsiteContentService;
use Illuminate\Http\Request;

class PlatformWebsiteContentController extends Controller
{
    public function index()
    {
        $sections = collect(WebsiteContentService::sections())
            ->map(function ($meta, $key) {
                $row = WebsiteContent::query()->where('key', $key)->first();

                return array_merge($meta, [
                    'key' => $key,
                    'updated_at' => $row?->updated_at,
                    'is_customized' => (bool) $row,
                ]);
            })
            ->sortBy('sort')
            ->values();

        return view('platform.website-content.index', compact('sections'));
    }

    public function edit(string $section)
    {
        abort_unless(array_key_exists($section, WebsiteContentService::sections()), 404);

        $meta = WebsiteContentService::sections()[$section];
        $content = WebsiteContentService::get($section);

        return view('platform.website-content.edit', [
            'section' => $section,
            'meta' => $meta,
            'content' => $content,
        ]);
    }

    public function update(Request $request, string $section)
    {
        abort_unless(array_key_exists($section, WebsiteContentService::sections()), 404);

        $meta = WebsiteContentService::sections()[$section];
        $content = $this->buildContent($request, $section);

        WebsiteContent::putContent(
            $section,
            $meta['label'],
            $content,
            $meta['group'],
            $meta['sort']
        );

        ActivityLogger::log('website_content_updated', "Website content updated: {$meta['label']}");

        return redirect()
            ->route('platform.website-content.edit', $section)
            ->with('success', "{$meta['label']} saved. Changes are live on the marketing site.");
    }

    public function reset(string $section)
    {
        abort_unless(array_key_exists($section, WebsiteContentService::sections()), 404);

        $meta = WebsiteContentService::sections()[$section];
        WebsiteContent::query()->where('key', $section)->delete();
        cache()->forget("website_content.{$section}");

        ActivityLogger::log('website_content_reset', "Website content reset: {$meta['label']}");

        return redirect()
            ->route('platform.website-content.edit', $section)
            ->with('success', "{$meta['label']} reset to defaults.");
    }

    protected function buildContent(Request $request, string $section): array
    {
        $current = WebsiteContentService::get($section);

        return match ($section) {
            'brand' => $request->validate([
                'name' => ['required', 'string', 'max:120'],
                'tagline' => ['nullable', 'string', 'max:255'],
                'email' => ['nullable', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'max:50'],
                'address' => ['nullable', 'string', 'max:1000'],
            ]),
            'video' => $request->validate([
                'youtube_id' => ['required', 'string', 'max:50'],
            ]),
            'apps', 'cta' => $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'text' => ['nullable', 'string', 'max:2000'],
            ]),
            'marketing', 'drm' => $this->splitSection($request, $current),
            'slides' => ['items' => $this->itemsWithImages($request, $current['items'] ?? [], ['title', 'text'], true)],
            'stats' => ['items' => $this->plainItems($request, ['value', 'label'])],
            'partners' => ['items' => $this->itemsWithImages($request, $current['items'] ?? [], ['name'])],
            'platform' => array_merge(
                $request->validate([
                    'heading_green' => ['nullable', 'string', 'max:80'],
                    'heading_blue' => ['nullable', 'string', 'max:80'],
                    'heading_rest' => ['nullable', 'string', 'max:160'],
                    'subheading' => ['nullable', 'string', 'max:1000'],
                ]),
                ['items' => $this->itemsWithImages($request, $current['items'] ?? [], ['slug', 'title', 'desc', 'bg'])]
            ),
            'domains' => array_merge(
                $request->validate([
                    'title' => ['required', 'string', 'max:255'],
                    'text' => ['nullable', 'string', 'max:1000'],
                ]),
                ['items' => $this->plainItems($request, ['slug', 'title', 'desc', 'type', 'icon'])]
            ),
            'support' => array_merge(
                $request->validate([
                    'title' => ['required', 'string', 'max:255'],
                    'text' => ['nullable', 'string', 'max:1000'],
                ]),
                ['items' => $this->plainItems($request, ['title', 'desc'])]
            ),
            'testimonials' => array_merge(
                $request->validate([
                    'title' => ['required', 'string', 'max:255'],
                    'text' => ['nullable', 'string', 'max:1000'],
                ]),
                ['items' => $this->plainItems($request, ['quote', 'name', 'role'])]
            ),
            'success_stories' => array_merge(
                $request->validate([
                    'title' => ['required', 'string', 'max:255'],
                    'text' => ['nullable', 'string', 'max:1000'],
                ]),
                ['items' => $this->plainItems($request, ['title', 'tag', 'date', 'read'])]
            ),
            default => [],
        };
    }

    protected function splitSection(Request $request, array $current): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'text' => ['nullable', 'string', 'max:2000'],
            'bullets' => ['nullable', 'string', 'max:4000'],
            'image' => ['nullable', 'image', 'max:5120'],
        ]);

        $image = $current['image'] ?? null;
        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('website/sections', 'public');
        }

        return [
            'title' => $validated['title'],
            'text' => $validated['text'] ?? '',
            'bullets' => $validated['bullets'] ?? '',
            'image' => $image,
        ];
    }

    protected function plainItems(Request $request, array $fields): array
    {
        $raw = $request->input('items', []);
        if (! is_array($raw)) {
            return [];
        }

        $items = [];
        foreach ($raw as $row) {
            if (! is_array($row)) {
                continue;
            }
            $item = [];
            $hasValue = false;
            foreach ($fields as $field) {
                $item[$field] = trim((string) ($row[$field] ?? ''));
                if ($item[$field] !== '') {
                    $hasValue = true;
                }
            }
            if ($hasValue) {
                $items[] = $item;
            }
        }

        return $items;
    }

    protected function itemsWithImages(Request $request, array $currentItems, array $fields, bool $withActive = false): array
    {
        $raw = $request->input('items', []);
        if (! is_array($raw)) {
            return [];
        }

        $files = $request->file('items', []);
        $items = [];

        foreach ($raw as $index => $row) {
            if (! is_array($row)) {
                continue;
            }

            $item = [];
            $hasValue = false;
            foreach ($fields as $field) {
                $item[$field] = trim((string) ($row[$field] ?? ''));
                if ($item[$field] !== '') {
                    $hasValue = true;
                }
            }

            $existingImage = $currentItems[$index]['image'] ?? ($row['existing_image'] ?? null);
            $item['image'] = $existingImage;

            if (isset($files[$index]['image']) && $files[$index]['image']) {
                $item['image'] = $files[$index]['image']->store('website/slides', 'public');
                $hasValue = true;
            }

            if ($withActive) {
                $item['is_active'] = ! empty($row['is_active']);
            }

            if ($hasValue || ! empty($item['image'])) {
                $items[] = $item;
            }
        }

        return $items;
    }
}
