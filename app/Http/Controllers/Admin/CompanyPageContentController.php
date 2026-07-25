<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ResolvesCompanyProfile;
use App\Http\Controllers\Controller;
use App\Models\CompanyBlog;
use App\Models\CompanyEnquiry;
use App\Models\CompanyGalleryItem;
use App\Models\CompanyReview;
use App\Models\CompanyTeamMember;
use App\Models\CompanyTestimonial;
use App\Models\CompanyVideo;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyPageContentController extends Controller
{
    use ResolvesCompanyProfile;

    public function testimonialsIndex(Request $request)
    {
        $company = $this->currentCompany();
        $status = $request->query('status');

        $items = $company->testimonials()
            ->when($status === 'published', fn ($q) => $q->where('is_published', true))
            ->when($status === 'hidden', fn ($q) => $q->where('is_published', false))
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        $stats = [
            'total' => $company->testimonials()->count(),
            'published' => $company->testimonials()->where('is_published', true)->count(),
            'hidden' => $company->testimonials()->where('is_published', false)->count(),
        ];

        return view('admin.company-content.testimonials.index', compact('company', 'items', 'stats', 'status'));
    }

    public function testimonialsStore(Request $request)
    {
        $company = $this->currentCompany();
        $data = $request->validate([
            'author_name' => ['required', 'string', 'max:120'],
            'author_title' => ['nullable', 'string', 'max:120'],
            'content' => ['required', 'string', 'max:2000'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'is_published' => ['nullable'],
        ]);

        $avatar = null;
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar')->store('companies/testimonials', 'public');
        }

        $company->testimonials()->create([
            'author_name' => $data['author_name'],
            'author_title' => $data['author_title'] ?? null,
            'content' => $data['content'],
            'rating' => $data['rating'],
            'avatar' => $avatar,
            'is_published' => $request->boolean('is_published', true),
            'sort_order' => (int) $company->testimonials()->count(),
        ]);

        ActivityLogger::log('company_testimonial_created', 'Testimonial added');

        return back()->with('success', 'Testimonial added.');
    }

    public function testimonialsUpdate(Request $request, CompanyTestimonial $testimonial)
    {
        $company = $this->currentCompany();
        $this->authorizeCompanyOwned($company, $testimonial->company_id);

        $data = $request->validate([
            'author_name' => ['required', 'string', 'max:120'],
            'author_title' => ['nullable', 'string', 'max:120'],
            'content' => ['required', 'string', 'max:2000'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'is_published' => ['nullable'],
        ]);

        $avatar = $testimonial->avatar;
        if ($request->hasFile('avatar')) {
            if ($avatar && ! str_starts_with($avatar, 'website/')) {
                Storage::disk('public')->delete($avatar);
            }
            $avatar = $request->file('avatar')->store('companies/testimonials', 'public');
        }

        $testimonial->update([
            'author_name' => $data['author_name'],
            'author_title' => $data['author_title'] ?? null,
            'content' => $data['content'],
            'rating' => $data['rating'],
            'avatar' => $avatar,
            'is_published' => $request->boolean('is_published'),
        ]);

        ActivityLogger::log('company_testimonial_updated', 'Testimonial updated', $testimonial);

        return back()->with('success', 'Testimonial updated.');
    }

    public function testimonialsToggle(CompanyTestimonial $testimonial)
    {
        $company = $this->currentCompany();
        $this->authorizeCompanyOwned($company, $testimonial->company_id);

        $testimonial->update([
            'is_published' => ! $testimonial->is_published,
        ]);

        $label = $testimonial->is_published ? 'published' : 'hidden';
        ActivityLogger::log('company_testimonial_toggled', "Testimonial marked {$label}", $testimonial);

        return back()->with('success', "Testimonial {$label}.");
    }

    public function testimonialsDestroy(CompanyTestimonial $testimonial)
    {
        $company = $this->currentCompany();
        $this->authorizeCompanyOwned($company, $testimonial->company_id);
        if ($testimonial->avatar && ! str_starts_with($testimonial->avatar, 'website/')) {
            Storage::disk('public')->delete($testimonial->avatar);
        }
        $testimonial->delete();

        return back()->with('success', 'Testimonial deleted.');
    }

    public function reviewsIndex(Request $request)
    {
        $company = $this->currentCompany();
        $status = $request->query('status');

        $items = $company->reviews()
            ->when($status === 'approved', fn ($q) => $q->where('is_approved', true))
            ->when($status === 'pending', fn ($q) => $q->where('is_approved', false))
            ->latest()
            ->get();

        $stats = [
            'total' => $company->reviews()->count(),
            'approved' => $company->reviews()->where('is_approved', true)->count(),
            'pending' => $company->reviews()->where('is_approved', false)->count(),
            'avg_rating' => round((float) $company->reviews()->where('is_approved', true)->avg('rating'), 1),
        ];

        return view('admin.company-content.reviews.index', compact('company', 'items', 'stats', 'status'));
    }

    public function reviewsApprove(CompanyReview $review)
    {
        $company = $this->currentCompany();
        $this->authorizeCompanyOwned($company, $review->company_id);
        $review->update(['is_approved' => true]);

        return back()->with('success', 'Review approved and published.');
    }

    public function reviewsReject(CompanyReview $review)
    {
        $company = $this->currentCompany();
        $this->authorizeCompanyOwned($company, $review->company_id);
        $review->update(['is_approved' => false]);

        return back()->with('success', 'Review unpublished.');
    }

    public function reviewsDestroy(CompanyReview $review)
    {
        $company = $this->currentCompany();
        $this->authorizeCompanyOwned($company, $review->company_id);
        $review->delete();

        return back()->with('success', 'Review deleted.');
    }

    public function enquiriesIndex(Request $request)
    {
        $company = $this->currentCompany();
        $status = $request->query('status');

        $items = $company->enquiries()
            ->when(in_array($status, ['new', 'read', 'replied'], true), fn ($q) => $q->where('status', $status))
            ->paginate(20);

        return view('admin.company-content.enquiries.index', compact('company', 'items'));
    }

    public function enquiriesMark(CompanyEnquiry $enquiry, string $status)
    {
        $company = $this->currentCompany();
        $this->authorizeCompanyOwned($company, $enquiry->company_id);
        abort_unless(in_array($status, ['new', 'read', 'replied'], true), 404);
        $enquiry->update(['status' => $status]);

        return back()->with('success', 'Enquiry marked as '.$status.'.');
    }

    public function enquiriesDestroy(CompanyEnquiry $enquiry)
    {
        $company = $this->currentCompany();
        $this->authorizeCompanyOwned($company, $enquiry->company_id);
        $enquiry->delete();

        return back()->with('success', 'Enquiry deleted.');
    }

    public function galleryIndex(Request $request)
    {
        $company = $this->currentCompany();
        $status = $request->query('status');

        $items = $company->galleryItems()
            ->when($status === 'published', fn ($q) => $q->where('is_published', true))
            ->when($status === 'hidden', fn ($q) => $q->where('is_published', false))
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(9)
            ->withQueryString();

        $stats = [
            'total' => $company->galleryItems()->count(),
            'published' => $company->galleryItems()->where('is_published', true)->count(),
            'hidden' => $company->galleryItems()->where('is_published', false)->count(),
        ];

        return view('admin.company-content.gallery.index', compact('company', 'items', 'stats', 'status'));
    }

    public function galleryStore(Request $request)
    {
        $company = $this->currentCompany();
        $request->validate([
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['image', 'max:5120'],
            'caption' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($request->file('images', []) as $file) {
            $company->galleryItems()->create([
                'image' => $file->store('companies/gallery', 'public'),
                'caption' => $request->input('caption'),
                'is_published' => true,
                'sort_order' => (int) $company->galleryItems()->count(),
            ]);
        }

        return back()->with('success', 'Gallery images uploaded.');
    }

    public function galleryUpdate(Request $request, CompanyGalleryItem $gallery)
    {
        $company = $this->currentCompany();
        $this->authorizeCompanyOwned($company, $gallery->company_id);

        $data = $request->validate([
            'caption' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:5120'],
            'is_published' => ['nullable'],
        ]);

        $image = $gallery->image;
        if ($request->hasFile('image')) {
            if ($image && ! str_starts_with($image, 'website/')) {
                Storage::disk('public')->delete($image);
            }
            $image = $request->file('image')->store('companies/gallery', 'public');
        }

        $gallery->update([
            'caption' => $data['caption'] ?? null,
            'image' => $image,
            'is_published' => $request->boolean('is_published'),
        ]);

        ActivityLogger::log('company_gallery_updated', 'Gallery item updated', $gallery);

        return back()->with('success', 'Gallery item updated.');
    }

    public function galleryToggle(CompanyGalleryItem $gallery)
    {
        $company = $this->currentCompany();
        $this->authorizeCompanyOwned($company, $gallery->company_id);

        $gallery->update([
            'is_published' => ! $gallery->is_published,
        ]);

        $label = $gallery->is_published ? 'published' : 'hidden';
        ActivityLogger::log('company_gallery_toggled', "Gallery item marked {$label}", $gallery);

        return back()->with('success', "Gallery image {$label}.");
    }

    public function galleryDestroy(CompanyGalleryItem $gallery)
    {
        $company = $this->currentCompany();
        $this->authorizeCompanyOwned($company, $gallery->company_id);
        if ($gallery->image && ! str_starts_with($gallery->image, 'website/')) {
            Storage::disk('public')->delete($gallery->image);
        }
        $gallery->delete();

        return back()->with('success', 'Image removed.');
    }

    public function videosIndex()
    {
        $company = $this->currentCompany();
        $items = $company->videos()->paginate(15);

        return view('admin.company-content.videos.index', compact('company', 'items'));
    }

    public function videosStore(Request $request)
    {
        $company = $this->currentCompany();
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:2000'],
            'video_url' => ['required', 'url', 'max:500'],
            'thumbnail' => ['nullable', 'image', 'max:4096'],
            'is_published' => ['nullable'],
        ]);

        $thumb = null;
        if ($request->hasFile('thumbnail')) {
            $thumb = $request->file('thumbnail')->store('companies/videos', 'public');
        }

        $company->videos()->create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'video_url' => $data['video_url'],
            'thumbnail' => $thumb,
            'is_published' => $request->boolean('is_published', true),
            'sort_order' => (int) $company->videos()->count(),
        ]);

        return back()->with('success', 'Video added.');
    }

    public function videosUpdate(Request $request, CompanyVideo $video)
    {
        $company = $this->currentCompany();
        $this->authorizeCompanyOwned($company, $video->company_id);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:2000'],
            'video_url' => ['required', 'url', 'max:500'],
            'thumbnail' => ['nullable', 'image', 'max:4096'],
            'is_published' => ['nullable'],
        ]);

        $thumb = $video->thumbnail;
        if ($request->hasFile('thumbnail')) {
            if ($thumb && ! str_starts_with($thumb, 'website/')) {
                Storage::disk('public')->delete($thumb);
            }
            $thumb = $request->file('thumbnail')->store('companies/videos', 'public');
        }

        $video->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'video_url' => $data['video_url'],
            'thumbnail' => $thumb,
            'is_published' => $request->boolean('is_published'),
        ]);

        return back()->with('success', 'Video updated.');
    }

    public function videosDestroy(CompanyVideo $video)
    {
        $company = $this->currentCompany();
        $this->authorizeCompanyOwned($company, $video->company_id);
        if ($video->thumbnail && ! str_starts_with($video->thumbnail, 'website/')) {
            Storage::disk('public')->delete($video->thumbnail);
        }
        $video->delete();

        return back()->with('success', 'Video deleted.');
    }

    public function blogsIndex()
    {
        $company = $this->currentCompany();
        $items = $company->blogs()->paginate(15);

        return view('admin.company-content.blogs.index', compact('company', 'items'));
    }

    public function blogsStore(Request $request)
    {
        $company = $this->currentCompany();
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'excerpt' => ['nullable', 'string', 'max:400'],
            'body' => ['nullable', 'string', 'max:20000'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
            'is_published' => ['nullable'],
        ]);

        $cover = null;
        if ($request->hasFile('cover_image')) {
            $cover = $request->file('cover_image')->store('companies/blogs', 'public');
        }

        $company->blogs()->create([
            'title' => $data['title'],
            'slug' => CompanyBlog::uniqueSlugForCompany($company->id, $data['title']),
            'excerpt' => $data['excerpt'] ?? null,
            'body' => $data['body'] ?? null,
            'cover_image' => $cover,
            'is_published' => $request->boolean('is_published', true),
            'published_at' => now(),
        ]);

        return back()->with('success', 'Blog post created.');
    }

    public function blogsUpdate(Request $request, CompanyBlog $blog)
    {
        $company = $this->currentCompany();
        $this->authorizeCompanyOwned($company, $blog->company_id);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'excerpt' => ['nullable', 'string', 'max:400'],
            'body' => ['nullable', 'string', 'max:20000'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
            'is_published' => ['nullable'],
        ]);

        $cover = $blog->cover_image;
        if ($request->hasFile('cover_image')) {
            if ($cover && ! str_starts_with($cover, 'website/')) {
                Storage::disk('public')->delete($cover);
            }
            $cover = $request->file('cover_image')->store('companies/blogs', 'public');
        }

        $blog->update([
            'title' => $data['title'],
            'slug' => CompanyBlog::uniqueSlugForCompany($company->id, $data['title'], $blog->id),
            'excerpt' => $data['excerpt'] ?? null,
            'body' => $data['body'] ?? null,
            'cover_image' => $cover,
            'is_published' => $request->boolean('is_published'),
            'published_at' => $blog->published_at ?: now(),
        ]);

        return back()->with('success', 'Blog post updated.');
    }

    public function blogsDestroy(CompanyBlog $blog)
    {
        $company = $this->currentCompany();
        $this->authorizeCompanyOwned($company, $blog->company_id);
        if ($blog->cover_image && ! str_starts_with($blog->cover_image, 'website/')) {
            Storage::disk('public')->delete($blog->cover_image);
        }
        $blog->delete();

        return back()->with('success', 'Blog post deleted.');
    }

    public function teamIndex()
    {
        $company = $this->currentCompany();
        $items = $company->teamMembers()->paginate(20);

        return view('admin.company-content.team.index', compact('company', 'items'));
    }

    public function teamStore(Request $request)
    {
        $company = $this->currentCompany();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'role' => ['nullable', 'string', 'max:120'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'is_published' => ['nullable'],
        ]);

        $photo = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo')->store('companies/team', 'public');
        }

        $company->teamMembers()->create([
            'name' => $data['name'],
            'role' => $data['role'] ?? null,
            'bio' => $data['bio'] ?? null,
            'photo' => $photo,
            'is_published' => $request->boolean('is_published', true),
            'sort_order' => (int) $company->teamMembers()->count(),
        ]);

        return back()->with('success', 'Team member added.');
    }

    public function teamUpdate(Request $request, CompanyTeamMember $member)
    {
        $company = $this->currentCompany();
        $this->authorizeCompanyOwned($company, $member->company_id);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'role' => ['nullable', 'string', 'max:120'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'is_published' => ['nullable'],
        ]);

        $photo = $member->photo;
        if ($request->hasFile('photo')) {
            if ($photo && ! str_starts_with($photo, 'website/')) {
                Storage::disk('public')->delete($photo);
            }
            $photo = $request->file('photo')->store('companies/team', 'public');
        }

        $member->update([
            'name' => $data['name'],
            'role' => $data['role'] ?? null,
            'bio' => $data['bio'] ?? null,
            'photo' => $photo,
            'is_published' => $request->boolean('is_published'),
        ]);

        return back()->with('success', 'Team member updated.');
    }

    public function teamDestroy(CompanyTeamMember $member)
    {
        $company = $this->currentCompany();
        $this->authorizeCompanyOwned($company, $member->company_id);
        if ($member->photo && ! str_starts_with($member->photo, 'website/')) {
            Storage::disk('public')->delete($member->photo);
        }
        $member->delete();

        return back()->with('success', 'Team member removed.');
    }
}
