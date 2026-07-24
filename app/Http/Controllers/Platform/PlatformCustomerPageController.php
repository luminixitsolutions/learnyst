<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\WebsiteContent;
use App\Services\ActivityLogger;
use App\Services\CustomerPageService;
use Illuminate\Http\Request;

class PlatformCustomerPageController extends Controller
{
    public function index()
    {
        $pages = collect(CustomerPageService::pages())
            ->map(function ($meta, $slug) {
                $content = CustomerPageService::get($slug);
                $row = WebsiteContent::query()->where('key', CustomerPageService::contentKey($slug))->first();

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

        return view('platform.customer-pages.index', compact('pages'));
    }

    public function edit(string $customerSlug)
    {
        abort_unless(array_key_exists($customerSlug, CustomerPageService::pages()), 404);

        $meta = CustomerPageService::pages()[$customerSlug];
        $content = CustomerPageService::get($customerSlug);
        $slug = $customerSlug;

        return view('platform.customer-pages.edit', compact('slug', 'meta', 'content'));
    }

    public function update(Request $request, string $customerSlug)
    {
        abort_unless(array_key_exists($customerSlug, CustomerPageService::pages()), 404);

        $meta = CustomerPageService::pages()[$customerSlug];
        $current = CustomerPageService::get($customerSlug);

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
            'stats' => ['nullable', 'array'],
            'stats.*.value' => ['nullable', 'string', 'max:40'],
            'stats.*.label' => ['nullable', 'string', 'max:120'],
            'items' => ['nullable', 'array'],
            'items.*.quote' => ['nullable', 'string', 'max:2000'],
            'items.*.name' => ['nullable', 'string', 'max:120'],
            'items.*.role' => ['nullable', 'string', 'max:160'],
            'items.*.result' => ['nullable', 'string', 'max:160'],
            'items.*.rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'items.*.featured' => ['nullable'],
            'items.*.source' => ['nullable', 'string', 'max:80'],
            'items.*.title' => ['nullable', 'string', 'max:255'],
            'items.*.tag' => ['nullable', 'string', 'max:80'],
            'items.*.date' => ['nullable', 'string', 'max:80'],
            'items.*.read' => ['nullable', 'string', 'max:40'],
            'items.*.summary' => ['nullable', 'string', 'max:500'],
            'items.*.metric' => ['nullable', 'string', 'max:40'],
            'items.*.metric_label' => ['nullable', 'string', 'max:80'],
        ]);

        $items = $this->mapItems($customerSlug, $validated['items'] ?? []);

        $payload = [
            'title' => $validated['title'],
            'caption' => $validated['caption'] ?? 'Customers',
            'eyebrow' => $validated['eyebrow'] ?? '',
            'summary' => $validated['summary'],
            'body' => $validated['body'],
            'hero_gradient' => $validated['hero_gradient'] ?? ($current['hero_gradient'] ?? ''),
            'cta_title' => $validated['cta_title'] ?? '',
            'cta_text' => $validated['cta_text'] ?? '',
            'cta_primary_label' => $validated['cta_primary_label'] ?? 'Start Free Trial',
            'cta_secondary_label' => $validated['cta_secondary_label'] ?? 'Book a Demo',
            'stats' => $this->mapRows($validated['stats'] ?? [], ['value', 'label'], 'value'),
            'items' => $items,
        ];

        WebsiteContent::putContent(
            CustomerPageService::contentKey($customerSlug),
            $meta['label'],
            $payload,
            'customers',
            $meta['sort']
        );

        // Keep homepage sections in sync for shared lists
        if ($customerSlug === 'testimonials') {
            $home = \App\Services\WebsiteContentService::get('testimonials');
            WebsiteContent::putContent('testimonials', 'Testimonials', [
                'title' => $home['title'] ?? 'Real Words, Real Impact',
                'text' => $home['text'] ?? '',
                'items' => array_map(fn ($i) => [
                    'quote' => $i['quote'] ?? '',
                    'name' => $i['name'] ?? '',
                    'role' => $i['role'] ?? '',
                ], $items),
            ], 'home', 120);
        }

        if ($customerSlug === 'success-stories') {
            $home = \App\Services\WebsiteContentService::get('success_stories');
            WebsiteContent::putContent('success_stories', 'Success Stories', [
                'title' => $home['title'] ?? 'Success Stories from Our Educators',
                'text' => $home['text'] ?? '',
                'items' => array_map(fn ($i) => [
                    'title' => $i['title'] ?? '',
                    'tag' => $i['tag'] ?? '',
                    'date' => $i['date'] ?? '',
                    'read' => $i['read'] ?? '',
                ], $items),
            ], 'home', 130);
        }

        ActivityLogger::log('customer_page_updated', "Customer page updated: {$meta['label']}");

        return redirect()
            ->route('platform.customer-pages.edit', $customerSlug)
            ->with('success', "{$meta['label']} page saved.");
    }

    public function reset(string $customerSlug)
    {
        abort_unless(array_key_exists($customerSlug, CustomerPageService::pages()), 404);

        $meta = CustomerPageService::pages()[$customerSlug];
        $key = CustomerPageService::contentKey($customerSlug);
        WebsiteContent::query()->where('key', $key)->delete();
        cache()->forget("website_content.{$key}");

        ActivityLogger::log('customer_page_reset', "Customer page reset: {$meta['label']}");

        return redirect()
            ->route('platform.customer-pages.edit', $customerSlug)
            ->with('success', "{$meta['label']} reset to defaults.");
    }

    protected function mapItems(string $slug, array $rows): array
    {
        if ($slug === 'testimonials') {
            return collect($rows)->map(function ($row) {
                $quote = trim((string) ($row['quote'] ?? ''));
                if ($quote === '') {
                    return null;
                }

                return [
                    'quote' => $quote,
                    'name' => trim((string) ($row['name'] ?? '')),
                    'role' => trim((string) ($row['role'] ?? '')),
                    'result' => trim((string) ($row['result'] ?? '')),
                    'rating' => max(1, min(5, (int) ($row['rating'] ?? 5))),
                    'featured' => ! empty($row['featured']),
                ];
            })->filter()->values()->all();
        }

        if ($slug === 'wall-of-love') {
            return collect($rows)->map(function ($row) {
                $quote = trim((string) ($row['quote'] ?? ''));
                if ($quote === '') {
                    return null;
                }

                return [
                    'quote' => $quote,
                    'name' => trim((string) ($row['name'] ?? '')),
                    'role' => trim((string) ($row['role'] ?? '')),
                    'source' => trim((string) ($row['source'] ?? '')),
                ];
            })->filter()->values()->all();
        }

        return collect($rows)->map(function ($row) {
            $title = trim((string) ($row['title'] ?? ''));
            if ($title === '') {
                return null;
            }

            return [
                'title' => $title,
                'tag' => trim((string) ($row['tag'] ?? '')),
                'date' => trim((string) ($row['date'] ?? '')),
                'read' => trim((string) ($row['read'] ?? '')),
                'summary' => trim((string) ($row['summary'] ?? '')),
                'metric' => trim((string) ($row['metric'] ?? '')),
                'metric_label' => trim((string) ($row['metric_label'] ?? '')),
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
