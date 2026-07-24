<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\WebsiteContent;
use App\Services\ActivityLogger;
use App\Services\ProductPageService;
use App\Services\SolutionPageService;
use Illuminate\Http\Request;

class PlatformSolutionPageController extends Controller
{
    public function index()
    {
        $solutions = collect(SolutionPageService::solutions())
            ->map(function ($meta, $slug) {
                $content = SolutionPageService::get($slug);
                $row = WebsiteContent::query()->where('key', SolutionPageService::contentKey($slug))->first();

                return array_merge($meta, [
                    'key' => $slug,
                    'title' => $content['title'] ?? $meta['label'],
                    'feature_count' => count($content['features'] ?? []),
                    'is_customized' => (bool) $row,
                    'updated_at' => $row?->updated_at,
                ]);
            })
            ->sortBy('sort')
            ->groupBy('group_label');

        return view('platform.solution-pages.index', compact('solutions'));
    }

    public function edit(string $solutionSlug)
    {
        abort_unless(array_key_exists($solutionSlug, SolutionPageService::solutions()), 404);

        $meta = SolutionPageService::solutions()[$solutionSlug];
        $content = SolutionPageService::get($solutionSlug);
        $slug = $solutionSlug;

        return view('platform.solution-pages.edit', compact('slug', 'meta', 'content'));
    }

    public function update(Request $request, string $solutionSlug)
    {
        abort_unless(array_key_exists($solutionSlug, SolutionPageService::solutions()), 404);

        $meta = SolutionPageService::solutions()[$solutionSlug];
        $current = SolutionPageService::get($solutionSlug);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:80'],
            'eyebrow' => ['nullable', 'string', 'max:80'],
            'summary' => ['required', 'string', 'max:500'],
            'body' => ['required', 'string', 'max:5000'],
            'hero_gradient' => ['nullable', 'string', 'max:255'],
            'hero_image' => ['nullable', 'image', 'max:5120'],
            'features' => ['nullable', 'string', 'max:4000'],
            'cta_title' => ['nullable', 'string', 'max:255'],
            'cta_text' => ['nullable', 'string', 'max:1000'],
            'cta_primary_label' => ['nullable', 'string', 'max:80'],
            'cta_secondary_label' => ['nullable', 'string', 'max:80'],
            'benefits' => ['nullable', 'array'],
            'benefits.*.icon' => ['nullable', 'string', 'max:60'],
            'benefits.*.title' => ['nullable', 'string', 'max:120'],
            'benefits.*.desc' => ['nullable', 'string', 'max:500'],
            'use_cases' => ['nullable', 'array'],
            'use_cases.*.title' => ['nullable', 'string', 'max:120'],
            'use_cases.*.desc' => ['nullable', 'string', 'max:500'],
            'stats' => ['nullable', 'array'],
            'stats.*.value' => ['nullable', 'string', 'max:40'],
            'stats.*.label' => ['nullable', 'string', 'max:120'],
            'faq' => ['nullable', 'array'],
            'faq.*.question' => ['nullable', 'string', 'max:255'],
            'faq.*.answer' => ['nullable', 'string', 'max:2000'],
        ]);

        $heroImage = $current['hero_image'] ?? null;
        if ($request->hasFile('hero_image')) {
            $heroImage = $request->file('hero_image')->store('website/solutions', 'public');
        }

        $payload = [
            'title' => $validated['title'],
            'caption' => $validated['caption'] ?? 'Solutions',
            'eyebrow' => $validated['eyebrow'] ?? '',
            'summary' => $validated['summary'],
            'body' => $validated['body'],
            'hero_gradient' => $validated['hero_gradient'] ?? ($current['hero_gradient'] ?? ''),
            'hero_image' => $heroImage,
            'features' => ProductPageService::normalizeList($validated['features'] ?? ''),
            'cta_title' => $validated['cta_title'] ?? '',
            'cta_text' => $validated['cta_text'] ?? '',
            'cta_primary_label' => $validated['cta_primary_label'] ?? 'Start Free Trial',
            'cta_secondary_label' => $validated['cta_secondary_label'] ?? 'Book a Demo',
            'benefits' => $this->mapRows($validated['benefits'] ?? [], ['icon', 'title', 'desc'], 'title'),
            'use_cases' => $this->mapRows($validated['use_cases'] ?? [], ['title', 'desc'], 'title'),
            'stats' => $this->mapRows($validated['stats'] ?? [], ['value', 'label'], 'value'),
            'faq' => $this->mapRows($validated['faq'] ?? [], ['question', 'answer'], 'question'),
        ];

        WebsiteContent::putContent(
            SolutionPageService::contentKey($solutionSlug),
            $meta['label'],
            $payload,
            'solutions',
            $meta['sort']
        );

        ActivityLogger::log('solution_page_updated', "Solution page updated: {$meta['label']}");

        return redirect()
            ->route('platform.solution-pages.edit', $solutionSlug)
            ->with('success', "{$meta['label']} page saved.");
    }

    public function reset(string $solutionSlug)
    {
        abort_unless(array_key_exists($solutionSlug, SolutionPageService::solutions()), 404);

        $meta = SolutionPageService::solutions()[$solutionSlug];
        $key = SolutionPageService::contentKey($solutionSlug);
        WebsiteContent::query()->where('key', $key)->delete();
        cache()->forget("website_content.{$key}");

        ActivityLogger::log('solution_page_reset', "Solution page reset: {$meta['label']}");

        return redirect()
            ->route('platform.solution-pages.edit', $solutionSlug)
            ->with('success', "{$meta['label']} reset to defaults.");
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
