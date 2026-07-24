<?php

namespace App\Http\Controllers;

use App\Services\BlogService;
use App\Services\CustomerPageService;
use App\Services\ProductPageService;
use App\Services\ResourcePageService;
use App\Services\SolutionPageService;
use App\Services\WebsiteContentService;

class WebsiteController extends Controller
{
    public function home()
    {
        WebsiteContentService::applyBrandToConfig();

        return view('website.home', WebsiteContentService::homePayload());
    }

    public function product(string $slug)
    {
        WebsiteContentService::applyBrandToConfig();
        $page = ProductPageService::get($slug);
        abort_unless($page, 404);

        return view('website.product', [
            'page' => $page,
            'type' => 'product',
            'slug' => $slug,
            'related' => ProductPageService::related($slug),
            'relatedRoute' => 'website.product',
        ]);
    }

    public function solution(string $slug)
    {
        WebsiteContentService::applyBrandToConfig();
        $page = SolutionPageService::get($slug);
        abort_unless($page, 404);

        return view('website.product', [
            'page' => $page,
            'type' => 'solution',
            'slug' => $slug,
            'related' => SolutionPageService::related($slug),
            'relatedRoute' => 'website.solution',
        ]);
    }

    public function page(string $slug)
    {
        WebsiteContentService::applyBrandToConfig();

        if (array_key_exists($slug, ResourcePageService::pages())) {
            $page = ResourcePageService::get($slug);
            abort_unless($page, 404);

            return view('website.resource', [
                'page' => $page,
                'type' => 'resource',
                'slug' => $slug,
            ]);
        }

        $page = config("website.pages.{$slug}");
        abort_unless($page, 404);

        return view('website.page', [
            'page' => $page,
            'type' => 'page',
            'slug' => $slug,
        ]);
    }

    public function helpCenter()
    {
        return $this->page('help-center');
    }

    public function whatsNew()
    {
        return $this->page('whats-new');
    }

    public function blogs()
    {
        WebsiteContentService::applyBrandToConfig();

        return view('website.blogs', [
            'page' => BlogService::getPage(),
        ]);
    }

    public function blogShow(string $slug)
    {
        WebsiteContentService::applyBrandToConfig();
        $post = BlogService::getPost($slug);
        abort_unless($post, 404);

        return view('website.blog-show', [
            'post' => $post,
            'relatedPosts' => BlogService::relatedPosts($slug),
        ]);
    }

    public function customer(string $slug)
    {
        WebsiteContentService::applyBrandToConfig();
        $page = CustomerPageService::get($slug);
        abort_unless($page, 404);

        return view('website.customers', [
            'page' => $page,
            'type' => 'customer',
            'slug' => $slug,
            'related' => CustomerPageService::related($slug),
        ]);
    }

    public function comparison(string $slug)
    {
        WebsiteContentService::applyBrandToConfig();
        $comparison = config("website.comparisons.{$slug}");
        abort_unless($comparison, 404);

        $page = [
            'title' => $comparison['title'],
            'caption' => 'Comparisons',
            'summary' => "See why institutes choose Learnyst as a {$comparison['competitor']} alternative.",
            'body' => "Compare Learnyst with {$comparison['competitor']} on DRM security, branded apps, mock tests, live classes, marketing tools, and support. Learnyst is built for institutes that need strong content protection and faster growth.",
            'features' => [
                'Best-in-class DRM content protection',
                'Branded website and mobile apps',
                'Mock tests, live classes, and communities',
                'Marketing and sales hub',
                'Dedicated educator support',
            ],
        ];

        return view('website.page', [
            'page' => $page,
            'type' => 'comparison',
            'slug' => $slug,
        ]);
    }
}
