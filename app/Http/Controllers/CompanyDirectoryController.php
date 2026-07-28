<?php

namespace App\Http\Controllers;

use App\Models\CompanyBlog;
use App\Models\CompanyEnquiry;
use App\Models\CompanyReview;
use App\Models\Course;
use App\Models\Lead;
use App\Services\ActivityLogger;
use App\Services\CompanyService;
use App\Services\WebsiteContentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyDirectoryController extends Controller
{
    public function index()
    {
        WebsiteContentService::applyBrandToConfig();

        $companies = CompanyService::publicCompanies(request('q'));

        return view('website.companies.index', compact('companies'));
    }

    public function show(Request $request, string $slug)
    {
        WebsiteContentService::applyBrandToConfig();

        $preview = false;
        if ($request->boolean('preview') && Auth::check()) {
            $user = Auth::user();
            if ($user->isSuperAdmin()) {
                $preview = true;
            } elseif ($user->isCompanyStaff()) {
                try {
                    $ownCompany = CompanyService::resolveForUser($user);
                    $preview = $ownCompany->slug === $slug;
                } catch (\Throwable $e) {
                    $preview = false;
                }
            }
        }

        $company = $preview
            ? CompanyService::findBySlug($slug)
            : CompanyService::findPublicBySlug($slug);
        abort_unless($company, 404);

        $courses = Course::query()
            ->published()
            ->where('created_by', $company->owner_user_id)
            ->with('category')
            ->latest()
            ->paginate(12);

        $testimonials = $company->testimonials()->published()->get();
        $reviews = $company->reviews()->approved()->latest()->take(12)->get();
        $videos = $company->videos()->published()->get();
        $blogs = $company->blogs()->published()->take(6)->get();
        $gallery = $company->galleryItems()->published()->get();
        $team = $company->teamMembers()->published()->get();
        $avgRating = round((float) $company->reviews()->approved()->avg('rating'), 1);
        $reviewCount = $company->reviews()->approved()->count();
        $brandCss = app(\App\Services\CompanyBrandingService::class)->cssVariables($company);
        app(\App\Services\CompanyBrandingService::class)->applyMailFrom($company);

        return view('website.companies.show', compact(
            'company',
            'courses',
            'testimonials',
            'reviews',
            'videos',
            'blogs',
            'gallery',
            'team',
            'avgRating',
            'reviewCount',
            'preview',
            'brandCss'
        ));
    }

    public function blog(string $slug, string $blogSlug)
    {
        WebsiteContentService::applyBrandToConfig();

        $company = CompanyService::findPublicBySlug($slug);
        abort_unless($company, 404);

        $blog = $company->blogs()->published()->where('slug', $blogSlug)->firstOrFail();

        return view('website.companies.blog', compact('company', 'blog'));
    }

    public function storeReview(Request $request, string $slug)
    {
        $company = CompanyService::findPublicBySlug($slug);
        abort_unless($company, 404);

        $data = $request->validate([
            'reviewer_name' => ['required', 'string', 'max:120'],
            'reviewer_email' => ['nullable', 'email', 'max:255'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'content' => ['required', 'string', 'max:2000'],
        ]);

        CompanyReview::create([
            'company_id' => $company->id,
            'user_id' => Auth::id(),
            'reviewer_name' => $data['reviewer_name'],
            'reviewer_email' => $data['reviewer_email'] ?? (Auth::user()?->email),
            'rating' => $data['rating'],
            'content' => $data['content'],
            'is_approved' => false,
        ]);

        return redirect()
            ->to(route('website.companies.show', $company->slug).'#reviews')
            ->with('success', 'Thanks! Your review was submitted and will appear after approval.');
    }

    public function storeEnquiry(Request $request, string $slug)
    {
        $company = CompanyService::findPublicBySlug($slug);
        abort_unless($company, 404);

        $validator = validator($request->all(), [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'subject' => ['nullable', 'string', 'max:180'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->to(route('website.companies.show', $company->slug).'#contact')
                ->withErrors($validator)
                ->withInput($request->all() + ['_form' => 'enquiry']);
        }

        $data = $validator->validated();

        $enquiry = CompanyEnquiry::create([
            'company_id' => $company->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'subject' => $data['subject'] ?: 'Course enquiry',
            'message' => $data['message'],
            'status' => 'new',
        ]);

        // Keep marketing leads in sync (same pattern as course enquiries).
        Lead::create([
            'created_by' => $company->owner_user_id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'course_id' => null,
            'source' => 'company_enquiry:'.$company->slug,
            'status' => 'new',
            'stage' => 'new',
            'notes' => trim(($enquiry->subject ? $enquiry->subject."\n" : '').$data['message']),
        ]);

        ActivityLogger::log(
            'company_enquiry_received',
            "Enquiry from {$enquiry->name} for {$company->name}",
            $enquiry
        );

        return redirect()
            ->to(route('website.companies.show', $company->slug).'#contact')
            ->with('success', 'Enquiry sent! The academy will contact you soon.')
            ->with('enquiry_company', $company->slug);
    }
}
