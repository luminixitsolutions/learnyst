<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\WebsiteContent;
use App\Services\ActivityLogger;
use App\Services\ProductPageService;
use App\Services\ResourcePageService;
use Illuminate\Http\Request;

class PlatformResourcePageController extends Controller
{
    public function index()
    {
        $pages = collect(ResourcePageService::pages())
            ->map(function ($meta, $slug) {
                $content = ResourcePageService::get($slug);
                $row = WebsiteContent::query()->where('key', ResourcePageService::contentKey($slug))->first();

                return array_merge($meta, [
                    'key' => $slug,
                    'title' => $content['title'] ?? $meta['label'],
                    'item_count' => count($content['items'] ?? []),
                    'is_customized' => (bool) $row,
                    'updated_at' => $row?->updated_at,
                ]);
            })
            ->sortBy('sort')
            ->values();

        return view('platform.resource-pages.index', compact('pages'));
    }

    public function edit(string $resourceSlug)
    {
        abort_unless(array_key_exists($resourceSlug, ResourcePageService::pages()), 404);

        $meta = ResourcePageService::pages()[$resourceSlug];
        $content = ResourcePageService::get($resourceSlug);
        $slug = $resourceSlug;

        return view('platform.resource-pages.edit', compact('slug', 'meta', 'content'));
    }

    public function update(Request $request, string $resourceSlug)
    {
        abort_unless(array_key_exists($resourceSlug, ResourcePageService::pages()), 404);

        $meta = ResourcePageService::pages()[$resourceSlug];
        $current = ResourcePageService::get($resourceSlug);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:80'],
            'eyebrow' => ['nullable', 'string', 'max:80'],
            'summary' => ['required', 'string', 'max:500'],
            'body' => ['required', 'string', 'max:5000'],
            'hero_gradient' => ['nullable', 'string', 'max:255'],
            'features' => ['nullable', 'string', 'max:4000'],
            'cta_title' => ['nullable', 'string', 'max:255'],
            'cta_text' => ['nullable', 'string', 'max:1000'],
            'cta_primary_label' => ['nullable', 'string', 'max:80'],
            'cta_secondary_label' => ['nullable', 'string', 'max:80'],
            'stats' => ['nullable', 'array'],
            'stats.*.value' => ['nullable', 'string', 'max:40'],
            'stats.*.label' => ['nullable', 'string', 'max:120'],
            'faq' => ['nullable', 'array'],
            'faq.*.question' => ['nullable', 'string', 'max:255'],
            'faq.*.answer' => ['nullable', 'string', 'max:2000'],
            'items' => ['nullable', 'array'],
            'items.*.icon' => ['nullable', 'string', 'max:60'],
            'items.*.title' => ['nullable', 'string', 'max:255'],
            'items.*.desc' => ['nullable', 'string', 'max:1000'],
            'items.*.tag' => ['nullable', 'string', 'max:80'],
            'items.*.read' => ['nullable', 'string', 'max:40'],
            'items.*.type' => ['nullable', 'string', 'max:40'],
            'items.*.date' => ['nullable', 'string', 'max:80'],
            'items.*.summary' => ['nullable', 'string', 'max:1000'],
            'items.*.highlights' => ['nullable', 'string', 'max:4000'],
        ]);

        $payload = [
            'title' => $validated['title'],
            'caption' => $validated['caption'] ?? 'Resources',
            'eyebrow' => $validated['eyebrow'] ?? '',
            'summary' => $validated['summary'],
            'body' => $validated['body'],
            'hero_gradient' => $validated['hero_gradient'] ?? ($current['hero_gradient'] ?? ''),
            'features' => ProductPageService::normalizeList($validated['features'] ?? ''),
            'cta_title' => $validated['cta_title'] ?? '',
            'cta_text' => $validated['cta_text'] ?? '',
            'cta_primary_label' => $validated['cta_primary_label'] ?? 'Start Free Trial',
            'cta_secondary_label' => $validated['cta_secondary_label'] ?? 'Book a Demo',
            'stats' => $this->mapRows($validated['stats'] ?? [], ['value', 'label'], 'value'),
            'faq' => $this->mapRows($validated['faq'] ?? [], ['question', 'answer'], 'question'),
            'items' => $this->mapItems($resourceSlug, $validated['items'] ?? []),
        ];

        WebsiteContent::putContent(
            ResourcePageService::contentKey($resourceSlug),
            $meta['label'],
            $payload,
            'resources',
            $meta['sort']
        );

        ActivityLogger::log('resource_page_updated', "Resource page updated: {$meta['label']}");

        return redirect()
            ->route('platform.resource-pages.edit', $resourceSlug)
            ->with('success', "{$meta['label']} page saved.");
    }

    public function reset(string $resourceSlug)
    {
        abort_unless(array_key_exists($resourceSlug, ResourcePageService::pages()), 404);

        $meta = ResourcePageService::pages()[$resourceSlug];
        $key = ResourcePageService::contentKey($resourceSlug);
        WebsiteContent::query()->where('key', $key)->delete();
        cache()->forget("website_content.{$key}");

        ActivityLogger::log('resource_page_reset', "Resource page reset: {$meta['label']}");

        return redirect()
            ->route('platform.resource-pages.edit', $resourceSlug)
            ->with('success', "{$meta['label']} reset to defaults.");
    }

    protected function mapItems(string $slug, array $rows): array
    {
        if ($slug === 'whats-new') {
            return collect($rows)->map(function ($row) {
                $title = trim((string) ($row['title'] ?? ''));
                if ($title === '') {
                    return null;
                }

                return [
                    'title' => $title,
                    'type' => trim((string) ($row['type'] ?? 'New')),
                    'date' => trim((string) ($row['date'] ?? '')),
                    'summary' => trim((string) ($row['summary'] ?? '')),
                    'highlights' => trim((string) ($row['highlights'] ?? '')),
                ];
            })->filter()->values()->all();
        }

        if ($slug === 'guides') {
            return collect($rows)->map(function ($row) {
                $title = trim((string) ($row['title'] ?? ''));
                if ($title === '') {
                    return null;
                }

                return [
                    'title' => $title,
                    'tag' => trim((string) ($row['tag'] ?? '')),
                    'read' => trim((string) ($row['read'] ?? '')),
                    'desc' => trim((string) ($row['desc'] ?? '')),
                ];
            })->filter()->values()->all();
        }

        return collect($rows)->map(function ($row) {
            $title = trim((string) ($row['title'] ?? ''));
            if ($title === '') {
                return null;
            }

            return [
                'icon' => trim((string) ($row['icon'] ?? 'fa-star')),
                'title' => $title,
                'desc' => trim((string) ($row['desc'] ?? '')),
            ];
        })->filter()->values()->all();
    }

    protected function mapRows(array $rows, array $fields, string $requiredField): array
    {
        $items = [];
        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $item = [];
            foreach ($fields as $field) {
                $item[$field] = trim((string) ($row[$field] ?? ''));
            }
            if ($item[$requiredField] !== '') {
                $items[] = $item;
            }
        }

        return $items;
    }
}
