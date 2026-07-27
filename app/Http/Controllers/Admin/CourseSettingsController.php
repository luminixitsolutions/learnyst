<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\ScopesToCurrentUser;
use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\Course;
use App\Models\CourseCertificateCriterion;
use App\Models\CourseEnrollment;
use App\Models\CourseFaq;
use App\Models\CourseLearnerRemoval;
use App\Models\CoursePricingPlan;
use App\Models\CoursePublicationHistory;
use App\Models\CourseSetting;
use App\Models\CourseSettingAuditLog;
use App\Models\Group;
use App\Models\Tag;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\CertificateDesignService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CourseSettingsController extends Controller
{
    use ScopesToCurrentUser;

    public function hub(Course $course)
    {
        $this->authorizeOwner($course);
        $settings = $this->ensureSettings($course);
        $statuses = $this->panelStatuses($course, $settings);
        $groups = config('course-settings.groups');
        $panels = config('course-settings.panels');

        return view('admin.courses.settings.hub', compact('course', 'settings', 'groups', 'panels', 'statuses'));
    }

    public function show(Course $course, string $panel)
    {
        $this->authorizeOwner($course);
        $panels = config('course-settings.panels');
        abort_unless(isset($panels[$panel]), 404);

        $settings = $this->ensureSettings($course);
        $meta = $panels[$panel];
        $groupKey = $meta['group'];
        $group = config("course-settings.groups.{$groupKey}");
        $groupPanels = collect($group['panels'])->mapWithKeys(fn ($key) => [$key => $panels[$key]]);

        $data = $this->panelData($course, $settings, $panel);

        return view('admin.courses.settings.panel', array_merge($data, [
            'course' => $course,
            'settings' => $settings,
            'panel' => $panel,
            'meta' => $meta,
            'groupKey' => $groupKey,
            'group' => $group,
            'groupPanels' => $groupPanels,
        ]));
    }

    public function update(Request $request, Course $course, string $panel)
    {
        $this->authorizeOwner($course);
        abort_unless(isset(config('course-settings.panels')[$panel]), 404);

        $settings = $this->ensureSettings($course);
        $before = $settings->toArray();

        $handler = match ($panel) {
            'branding' => 'updateBranding',
            'seo' => 'updateSeo',
            'tags' => 'updateTags',
            'faqs' => 'updateFaqs',
            'instructors' => 'updateInstructors',
            'pricing-plans' => 'updatePricingPlansMeta',
            'android-pricing' => 'updateAndroidPricing',
            'ios-pricing' => 'updateIosPricing',
            'permissions' => 'updatePermissions',
            'ratings-reviews' => 'updateRatingsReviews',
            'discussions-bookmarks' => 'updateDiscussionsBookmarks',
            'leaderboard' => 'updateLeaderboard',
            'certificates' => 'updateCertificates',
            'content-dripping' => 'updateContentDripping',
            'learner-configurations' => 'updateLearnerConfigurations',
            'learning-path' => 'updateLearningPath',
            default => null,
        };

        abort_unless($handler, 404);
        $this->{$handler}($request, $course, $settings);

        $this->audit($course, $panel, 'updated', $before, $settings->fresh()->toArray());

        return back()->with('success', 'Settings saved successfully.');
    }

    public function publish(Request $request, Course $course)
    {
        $this->authorizeOwner($course);
        $settings = $this->ensureSettings($course);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['published', 'unpublished', 'draft'])],
            'confirm' => ['accepted'],
        ]);

        $missing = $this->publishRequirements($course);
        if ($validated['status'] === 'published' && ! empty($missing)) {
            return back()->withErrors([
                'status' => 'Cannot publish until required information is complete.',
            ])->with('publish_missing', $missing);
        }

        $from = $course->status;
        $course->update(['status' => $validated['status']]);

        if ($validated['status'] === 'published') {
            $settings->update([
                'published_at' => now(),
                'published_by' => Auth::id(),
            ]);
        }

        CoursePublicationHistory::create([
            'course_id' => $course->id,
            'from_status' => $from,
            'to_status' => $validated['status'],
            'changed_by' => Auth::id(),
            'notes' => $request->input('notes'),
        ]);

        ActivityLogger::log('course_status_changed', "Course {$course->title} set to {$validated['status']}", $course);
        $this->audit($course, 'publish', 'status_changed', ['status' => $from], ['status' => $validated['status']]);

        return back()->with('success', 'Course status updated.');
    }

    public function trash(Request $request, Course $course)
    {
        $this->authorizeOwner($course);
        $settings = $this->ensureSettings($course);

        $validated = $request->validate([
            'course_name' => ['required', 'string'],
            'reason' => ['nullable', 'string', 'max:1000'],
            'confirm' => ['accepted'],
        ]);

        if (trim($validated['course_name']) !== $course->title) {
            return back()->withErrors(['course_name' => 'Course name does not match.']);
        }

        $blockers = $this->trashBlockers($course);
        if (! empty($blockers)) {
            return back()->withErrors(['course_name' => 'Cannot move to trash while protected dependencies exist.'])
                ->with('trash_blockers', $blockers);
        }

        $settings->update([
            'deleted_by' => Auth::id(),
            'deletion_reason' => $validated['reason'] ?? null,
        ]);

        $course->delete();
        ActivityLogger::log('course_trashed', "Course {$course->title} moved to trash", $course);
        $this->audit($course, 'trash', 'soft_deleted', [], ['reason' => $validated['reason'] ?? null]);

        return redirect()->route('admin.courses.index')->with('success', 'Course moved to trash.');
    }

    public function restore(int $course)
    {
        $trashed = Course::onlyTrashed()->findOrFail($course);
        $this->authorizeOwner($trashed);

        $trashed->restore();
        if ($trashed->settings) {
            $trashed->settings->update([
                'deleted_by' => null,
                'deletion_reason' => null,
            ]);
        }

        ActivityLogger::log('course_restored', "Course {$trashed->title} restored from trash", $trashed);
        $this->audit($trashed, 'trash', 'restored', [], []);

        return redirect()->route('admin.courses.settings.hub', $trashed)->with('success', 'Course restored.');
    }

    public function removeLearners(Request $request, Course $course)
    {
        $this->authorizeOwner($course);

        $validated = $request->validate([
            'course_name' => ['required', 'string'],
            'learner_ids' => ['required', 'array', 'min:1'],
            'learner_ids.*' => ['integer'],
            'reason' => ['nullable', 'string', 'max:1000'],
            'confirm' => ['accepted'],
        ]);

        if (trim($validated['course_name']) !== $course->title) {
            return back()->withErrors(['course_name' => 'Course name does not match.']);
        }

        $enrollments = CourseEnrollment::with('user')
            ->where('course_id', $course->id)
            ->whereIn('user_id', $validated['learner_ids'])
            ->get();

        DB::transaction(function () use ($enrollments, $course, $validated) {
            foreach ($enrollments as $enrollment) {
                CourseLearnerRemoval::create([
                    'course_id' => $course->id,
                    'user_id' => $enrollment->user_id,
                    'enrollment_id' => $enrollment->id,
                    'removed_by' => Auth::id(),
                    'reason' => $validated['reason'] ?? null,
                    'snapshot' => $enrollment->toArray(),
                ]);

                $enrollment->update(['status' => 'revoked']);
            }
        });

        $this->audit($course, 'remove-learners', 'removed', [], [
            'count' => $enrollments->count(),
            'learner_ids' => $validated['learner_ids'],
        ]);

        return back()->with('success', $enrollments->count().' learner(s) removed from this course. Payment history was kept.');
    }

    public function restoreLearner(Course $course, CourseLearnerRemoval $removal)
    {
        $this->authorizeOwner($course);
        abort_unless($removal->course_id === $course->id, 404);
        abort_unless($removal->restored_at === null, 422);

        DB::transaction(function () use ($removal) {
            if ($removal->enrollment_id) {
                CourseEnrollment::whereKey($removal->enrollment_id)->update([
                    'status' => 'active',
                ]);
            } else {
                CourseEnrollment::create([
                    'user_id' => $removal->user_id,
                    'course_id' => $removal->course_id,
                    'enrollment_type' => 'course',
                    'status' => 'active',
                    'enrolled_at' => now(),
                    'access_starts_at' => now(),
                ]);
            }

            $removal->update([
                'restored_at' => now(),
                'restored_by' => Auth::id(),
            ]);
        });

        return back()->with('success', 'Learner enrollment restored.');
    }

    public function storePricingPlan(Request $request, Course $course)
    {
        $this->authorizeOwner($course);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'plan_type' => ['required', Rule::in(['free', 'one_time', 'limited_offer', 'subscription', 'installment', 'custom'])],
            'regular_price' => ['nullable', 'numeric', 'min:0'],
            'offer_price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'validity_days' => ['nullable', 'integer', 'min:1'],
            'lifetime_access' => ['boolean'],
            'description' => ['nullable', 'string'],
            'trial_days' => ['nullable', 'integer', 'min:0'],
            'purchase_starts_at' => ['nullable', 'date'],
            'purchase_ends_at' => ['nullable', 'date', 'after_or_equal:purchase_starts_at'],
            'offer_starts_at' => ['nullable', 'date'],
            'offer_ends_at' => ['nullable', 'date', 'after:offer_starts_at'],
            'enrollment_limit' => ['nullable', 'integer', 'min:1'],
            'is_public' => ['boolean'],
            'coupon_eligible' => ['boolean'],
            'billing_frequency' => ['nullable', 'string', 'max:40'],
            'setup_fee' => ['nullable', 'numeric', 'min:0'],
            'auto_renew' => ['boolean'],
            'show_countdown' => ['boolean'],
        ]);

        $validated['course_id'] = $course->id;
        $validated['created_by'] = Auth::id();
        $validated['status'] = 'draft';
        $validated['slug'] = Str::slug($validated['title']).'-'.Str::random(4);
        $validated['lifetime_access'] = $request->boolean('lifetime_access');
        $validated['is_public'] = $request->boolean('is_public', true);
        $validated['coupon_eligible'] = $request->boolean('coupon_eligible', true);
        $validated['auto_renew'] = $request->boolean('auto_renew', true);
        $validated['show_countdown'] = $request->boolean('show_countdown');
        $validated['currency'] = $validated['currency'] ?? 'INR';

        if ($validated['plan_type'] === 'free') {
            $validated['regular_price'] = 0;
            $validated['offer_price'] = 0;
        }

        CoursePricingPlan::create($validated);
        $this->audit($course, 'pricing-plans', 'created', [], $validated);

        return back()->with('success', 'Pricing plan created as draft.');
    }

    public function updatePricingPlanStatus(Request $request, Course $course, CoursePricingPlan $plan)
    {
        $this->authorizeOwner($course);
        abort_unless($plan->course_id === $course->id, 404);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['draft', 'published', 'unpublished', 'archived'])],
        ]);

        $plan->update(['status' => $validated['status']]);

        return back()->with('success', 'Pricing plan status updated.');
    }

    public function destroyPricingPlan(Course $course, CoursePricingPlan $plan)
    {
        $this->authorizeOwner($course);
        abort_unless($plan->course_id === $course->id, 404);
        $plan->delete();

        return back()->with('success', 'Pricing plan deleted.');
    }

    public function duplicatePricingPlan(Course $course, CoursePricingPlan $plan)
    {
        $this->authorizeOwner($course);
        abort_unless($plan->course_id === $course->id, 404);

        $copy = $plan->replicate(['enrollment_count', 'sales_count']);
        $copy->title = $plan->title.' (Copy)';
        $copy->slug = Str::slug($copy->title).'-'.Str::random(4);
        $copy->status = 'draft';
        $copy->created_by = Auth::id();
        $copy->save();

        return back()->with('success', 'Pricing plan duplicated.');
    }

    public function storeFaq(Request $request, Course $course)
    {
        $this->authorizeOwner($course);
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string'],
        ]);

        $validated['course_id'] = $course->id;
        $validated['sort_order'] = ($course->faqs()->max('sort_order') ?? 0) + 1;
        CourseFaq::create($validated);

        return back()->with('success', 'FAQ added.');
    }

    public function destroyFaq(Course $course, CourseFaq $faq)
    {
        $this->authorizeOwner($course);
        abort_unless($faq->course_id === $course->id, 404);
        $faq->delete();

        return back()->with('success', 'FAQ removed.');
    }

    public function storeCertificateCriterion(Request $request, Course $course)
    {
        $this->authorizeOwner($course);
        $validated = $request->validate([
            'criterion_type' => ['required', 'string', 'max:60'],
            'logic' => ['required', Rule::in(['and', 'or'])],
            'is_mandatory' => ['boolean'],
            'config' => ['nullable', 'array'],
        ]);

        CourseCertificateCriterion::create([
            'course_id' => $course->id,
            'criterion_type' => $validated['criterion_type'],
            'logic' => $validated['logic'],
            'is_mandatory' => $request->boolean('is_mandatory', true),
            'config' => $validated['config'] ?? [],
            'sort_order' => ($course->certificateCriteria()->max('sort_order') ?? 0) + 1,
        ]);

        return back()->with('success', 'Certificate criterion added.');
    }

    public function destroyCertificateCriterion(Course $course, CourseCertificateCriterion $criterion)
    {
        $this->authorizeOwner($course);
        abort_unless($criterion->course_id === $course->id, 404);
        $criterion->delete();

        return back()->with('success', 'Criterion removed.');
    }

    /* -------------------- panel data -------------------- */

    protected function panelData(Course $course, CourseSetting $settings, string $panel): array
    {
        return match ($panel) {
            'branding' => [
                'categories' => Category::where('is_active', true)->orderBy('name')->get(),
            ],
            'seo' => [],
            'tags' => [
                'tags' => Tag::orderBy('name')->get(),
                'selectedTagIds' => $course->tags()->pluck('tags.id')->all(),
            ],
            'faqs' => [
                'faqs' => $course->faqs()->orderBy('sort_order')->get(),
            ],
            'instructors' => [
                'instructors' => $this->ownedUsersQuery('instructor')->orderBy('name')->get(),
                'assigned' => $course->instructors()->get(),
            ],
            'pricing-plans' => [
                'plans' => $course->pricingPlans()->latest()->get(),
                'statusFilter' => request('status', 'all'),
                'search' => request('search'),
            ],
            'android-pricing', 'ios-pricing' => [],
            'permissions' => [],
            'ratings-reviews' => [
                'reviews' => $course->reviews()->with('user')->latest()->paginate(15),
            ],
            'discussions-bookmarks', 'leaderboard', 'content-dripping', 'learner-configurations', 'learning-path' => [],
            'certificates' => [
                'presets' => app(CertificateDesignService::class)->presets(),
                'courseTemplate' => app(CertificateDesignService::class)->forCourse($course),
                'designService' => app(CertificateDesignService::class),
                'selectedPresetKey' => app(CertificateDesignService::class)->resolvePresetKey(
                    app(CertificateDesignService::class)->layoutFrom(
                        app(CertificateDesignService::class)->forCourse($course)
                    )
                ),
                'elementPositions' => app(CertificateDesignService::class)->sanitizeElementPositions(
                    app(CertificateDesignService::class)->layoutFrom(
                        app(CertificateDesignService::class)->forCourse($course)
                    )['element_positions'] ?? []
                ),
                'elementLabels' => CertificateDesignService::ELEMENT_LABELS,
                'criteria' => $course->certificateCriteria()->orderBy('sort_order')->get(),
                'issuedCount' => Certificate::where('course_id', $course->id)->count(),
            ],
            'publish' => [
                'missing' => $this->publishRequirements($course),
                'history' => $course->publicationHistories()->with('changedBy')->latest()->take(10)->get(),
            ],
            'trash' => [
                'blockers' => $this->trashBlockers($course),
                'retentionDays' => $settings->trash_retention_days ?? 30,
            ],
            'associated-contents' => [
                'associations' => $this->associations($course),
            ],
            'remove-learners' => [
                'learners' => CourseEnrollment::with('user')
                    ->where('course_id', $course->id)
                    ->when(request('search'), function ($q) {
                        $s = request('search');
                        $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$s}%")
                            ->orWhere('email', 'like', "%{$s}%")
                            ->orWhere('phone', 'like', "%{$s}%"));
                    })
                    ->latest()
                    ->paginate(20)
                    ->withQueryString(),
                'removals' => CourseLearnerRemoval::with(['user', 'removedBy'])
                    ->where('course_id', $course->id)
                    ->latest()
                    ->take(20)
                    ->get(),
            ],
            default => [],
        };
    }

    /* -------------------- update handlers -------------------- */

    protected function updateBranding(Request $request, Course $course, CourseSetting $settings): void
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'intro_video_url' => ['nullable', 'url'],
            'thumbnail' => ['nullable', 'image', 'max:4096'],
            'language' => ['nullable', 'string', 'max:40'],
            'difficulty' => ['nullable', 'string', 'max:40'],
            'estimated_duration' => ['nullable', 'string', 'max:40'],
            'is_featured' => ['boolean'],
            'accent_color' => ['nullable', 'string', 'max:20'],
            'remove_thumbnail' => ['boolean'],
        ]);

        if ($request->boolean('remove_thumbnail') && $course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
            $validated['thumbnail'] = null;
        }

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('courses', 'public');
        }

        $course->update(collect($validated)->only([
            'title', 'subtitle', 'description', 'category_id', 'intro_video_url', 'thumbnail',
        ])->filter(fn ($v, $k) => $request->has($k) || $k === 'thumbnail')->all());

        $branding = array_merge($settings->branding ?? [], [
            'language' => $validated['language'] ?? null,
            'difficulty' => $validated['difficulty'] ?? null,
            'estimated_duration' => $validated['estimated_duration'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
            'accent_color' => $validated['accent_color'] ?? null,
        ]);

        $settings->update(['branding' => $branding]);
    }

    protected function updateSeo(Request $request, Course $course, CourseSetting $settings): void
    {
        $validated = $request->validate([
            'slug' => ['required', 'string', 'max:255', Rule::unique('courses', 'slug')->ignore($course->id)],
            'seo_title' => ['nullable', 'string', 'max:70'],
            'seo_description' => ['nullable', 'string', 'max:160'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'canonical_url' => ['nullable', 'url'],
            'og_title' => ['nullable', 'string', 'max:70'],
            'og_description' => ['nullable', 'string', 'max:200'],
            'robots_index' => ['boolean'],
            'robots_follow' => ['boolean'],
            'og_image' => ['nullable', 'image', 'max:4096'],
        ]);

        $course->update([
            'slug' => Str::slug($validated['slug']),
            'seo_title' => $validated['seo_title'] ?? null,
            'seo_description' => $validated['seo_description'] ?? null,
        ]);

        $seo = array_merge($settings->seo ?? [], [
            'meta_keywords' => $validated['meta_keywords'] ?? null,
            'canonical_url' => $validated['canonical_url'] ?? null,
            'og_title' => $validated['og_title'] ?? null,
            'og_description' => $validated['og_description'] ?? null,
            'robots_index' => $request->boolean('robots_index', true),
            'robots_follow' => $request->boolean('robots_follow', true),
        ]);

        if ($request->hasFile('og_image')) {
            $seo['og_image'] = $request->file('og_image')->store('courses/seo', 'public');
        }

        $settings->update(['seo' => $seo]);
    }

    protected function updateTags(Request $request, Course $course, CourseSetting $settings): void
    {
        $validated = $request->validate([
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
            'new_tag' => ['nullable', 'string', 'max:80'],
        ]);

        if ($request->filled('new_tag')) {
            $tag = Tag::firstOrCreate(
                ['slug' => Str::slug($validated['new_tag'])],
                ['name' => $validated['new_tag']]
            );
            $validated['tags'] = array_unique(array_merge($validated['tags'] ?? [], [$tag->id]));
        }

        $course->tags()->sync($validated['tags'] ?? []);
    }

    protected function updateFaqs(Request $request, Course $course, CourseSetting $settings): void
    {
        // FAQs managed via storeFaq/destroyFaq; allow reorder payload
        $validated = $request->validate([
            'order' => ['nullable', 'array'],
            'order.*' => ['integer'],
        ]);

        foreach ($validated['order'] ?? [] as $index => $id) {
            CourseFaq::where('course_id', $course->id)->where('id', $id)->update(['sort_order' => $index]);
        }
    }

    protected function updateInstructors(Request $request, Course $course, CourseSetting $settings): void
    {
        $ownedIds = $this->ownedUsersQuery('instructor')->pluck('id')->all();

        $validated = $request->validate([
            'instructor_ids' => ['nullable', 'array'],
            'instructor_ids.*' => ['integer', Rule::in($ownedIds)],
            'primary_instructor_id' => ['nullable', 'integer', Rule::in($ownedIds)],
        ]);

        $ids = $validated['instructor_ids'] ?? [];
        $primary = $validated['primary_instructor_id'] ?? ($ids[0] ?? null);

        $sync = collect($ids)->mapWithKeys(fn ($id) => [
            $id => ['is_primary' => (int) $id === (int) $primary],
        ])->all();

        $course->instructors()->sync($sync);
    }

    protected function updatePricingPlansMeta(Request $request, Course $course, CourseSetting $settings): void
    {
        // Status/create handled by dedicated endpoints
    }

    protected function updateAndroidPricing(Request $request, Course $course, CourseSetting $settings): void
    {
        $validated = $request->validate([
            'enabled' => ['boolean'],
            'product_id' => ['nullable', 'string', 'max:120'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'subscription_id' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $settings->update([
            'android_pricing' => [
                'enabled' => $request->boolean('enabled'),
                'product_id' => $validated['product_id'] ?? null,
                'price' => $validated['price'] ?? null,
                'subscription_id' => $validated['subscription_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ],
        ]);
    }

    protected function updateIosPricing(Request $request, Course $course, CourseSetting $settings): void
    {
        $validated = $request->validate([
            'enabled' => ['boolean'],
            'product_id' => ['nullable', 'string', 'max:120'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'subscription_id' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $settings->update([
            'ios_pricing' => [
                'enabled' => $request->boolean('enabled'),
                'product_id' => $validated['product_id'] ?? null,
                'price' => $validated['price'] ?? null,
                'subscription_id' => $validated['subscription_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ],
        ]);
    }

    protected function updatePermissions(Request $request, Course $course, CourseSetting $settings): void
    {
        $validated = $request->validate([
            'sell_independently' => ['boolean'],
            'access_visibility' => ['required', Rule::in(['public', 'private', 'unlisted', 'invitation', 'membership', 'organization'])],
            'selling_platforms' => ['nullable', 'array'],
            'selling_platforms.*' => [Rule::in(['all', 'web', 'android', 'ios'])],
            'allow_guest_preview' => ['boolean'],
            'allow_manual_enrollment' => ['boolean'],
            'allow_instructor_enrollment' => ['boolean'],
            'allow_batch_enrollment' => ['boolean'],
            'offline_sync' => ['boolean'],
            'max_active_sessions' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $platforms = $validated['selling_platforms'] ?? ['all'];
        if (in_array('all', $platforms, true)) {
            $platforms = ['all'];
        }

        $settings->update([
            'sell_independently' => $request->boolean('sell_independently', true),
            'access_visibility' => $validated['access_visibility'],
            'selling_platforms' => $platforms,
            'permissions' => [
                'allow_guest_preview' => $request->boolean('allow_guest_preview'),
                'allow_manual_enrollment' => $request->boolean('allow_manual_enrollment', true),
                'allow_instructor_enrollment' => $request->boolean('allow_instructor_enrollment'),
                'allow_batch_enrollment' => $request->boolean('allow_batch_enrollment', true),
                'offline_sync' => $request->boolean('offline_sync'),
                'max_active_sessions' => $validated['max_active_sessions'] ?? null,
            ],
        ]);
    }

    protected function updateRatingsReviews(Request $request, Course $course, CourseSetting $settings): void
    {
        $validated = $request->validate([
            'reviews_enabled' => ['boolean'],
            'written_reviews' => ['boolean'],
            'enrolled_only' => ['boolean'],
            'min_completion_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'allow_anonymous' => ['boolean'],
            'require_moderation' => ['boolean'],
            'allow_edit' => ['boolean'],
            'min_rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'max_rating' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        $settings->update([
            'reviews_enabled' => $request->boolean('reviews_enabled'),
            'review_config' => [
                'written_reviews' => $request->boolean('written_reviews', true),
                'enrolled_only' => $request->boolean('enrolled_only', true),
                'min_completion_percent' => $validated['min_completion_percent'] ?? 0,
                'allow_anonymous' => $request->boolean('allow_anonymous'),
                'require_moderation' => $request->boolean('require_moderation', true),
                'allow_edit' => $request->boolean('allow_edit'),
                'min_rating' => $validated['min_rating'] ?? 1,
                'max_rating' => $validated['max_rating'] ?? 5,
            ],
        ]);
    }

    protected function updateDiscussionsBookmarks(Request $request, Course $course, CourseSetting $settings): void
    {
        $settings->update([
            'discussion_enabled' => $request->boolean('discussion_enabled'),
            'bookmarks_enabled' => $request->boolean('bookmarks_enabled', true),
            'discussion_config' => [
                'public' => $request->boolean('public_discussions', true),
                'private' => $request->boolean('private_discussions'),
                'allow_replies' => $request->boolean('allow_replies', true),
                'allow_attachments' => $request->boolean('allow_attachments'),
                'instructor_announcements' => $request->boolean('instructor_announcements', true),
                'moderation' => $request->boolean('moderation'),
                'report_flag' => $request->boolean('report_flag', true),
                'notifications' => $request->boolean('notifications', true),
            ],
            'bookmark_config' => [
                'allow_notes' => $request->boolean('allow_bookmark_notes', true),
                'sync_progress' => $request->boolean('sync_bookmark_progress', true),
            ],
        ]);
    }

    protected function updateLeaderboard(Request $request, Course $course, CourseSetting $settings): void
    {
        $validated = $request->validate([
            'ranking_by' => ['nullable', 'array'],
            'ranking_by.*' => [Rule::in(['completion', 'quiz', 'assignment', 'time', 'points', 'certificate'])],
            'period' => ['nullable', Rule::in(['daily', 'weekly', 'monthly', 'all_time'])],
        ]);

        $settings->update([
            'leaderboard_enabled' => $request->boolean('leaderboard_enabled'),
            'leaderboard_config' => [
                'course_only' => $request->boolean('course_only', true),
                'assignment_based' => $request->boolean('assignment_based'),
                'ranking_by' => $validated['ranking_by'] ?? ['completion'],
                'period' => $validated['period'] ?? 'all_time',
                'privacy_mode' => $request->boolean('privacy_mode'),
            ],
        ]);
    }

    protected function updateCertificates(Request $request, Course $course, CourseSetting $settings): void
    {
        $design = app(CertificateDesignService::class);

        $validated = $request->validate([
            'preset_key' => ['nullable', 'string', Rule::in($design->presetKeys())],
            'element_positions' => ['nullable', 'string'],
            'certificate_template_id' => ['nullable', 'exists:certificate_templates,id'],
            'auto_generate' => ['boolean'],
            'expiry_days' => ['nullable', 'integer', 'min:1'],
            'unique_number' => ['boolean'],
            'qr_verification' => ['boolean'],
        ]);

        $template = $design->forCourse($course);

        if ($request->filled('preset_key')) {
            $design->applyPreset($template, $validated['preset_key']);
            $template = $template->fresh();
            $design->attachToCourse($course, $template);
            $validated['certificate_template_id'] = $template->id;
        }

        if ($request->filled('element_positions')) {
            $decoded = json_decode($request->input('element_positions'), true);
            if (is_array($decoded)) {
                $layout = $design->layoutFrom($template);
                $layout['element_positions'] = $design->sanitizeElementPositions($decoded);
                $design->saveDesign($template, $layout);
                $template = $template->fresh();
            }
        }

        $settings->refresh();
        $settings->update([
            'certificate_enabled' => $request->boolean('certificate_enabled'),
            'certificate_config' => [
                'certificate_template_id' => $validated['certificate_template_id'] ?? $template->id,
                'preset_key' => $validated['preset_key'] ?? $design->resolvePresetKey($design->layoutFrom($template)),
                'auto_generate' => $request->boolean('auto_generate', true),
                'expiry_days' => $validated['expiry_days'] ?? null,
                'unique_number' => $request->boolean('unique_number', true),
                'qr_verification' => $request->boolean('qr_verification', true),
            ],
        ]);
    }

    protected function updateContentDripping(Request $request, Course $course, CourseSetting $settings): void
    {
        $validated = $request->validate([
            'drip_mode' => ['required', Rule::in(['immediate', 'after_enrollment', 'calendar', 'previous_completion', 'days_after', 'weekly', 'monthly', 'manual'])],
            'days_after_enrollment' => ['nullable', 'integer', 'min:0'],
            'locked_message' => ['nullable', 'string', 'max:500'],
            'timezone' => ['nullable', 'string', 'max:60'],
        ]);

        $course->update(['drip_enabled' => $validated['drip_mode'] !== 'immediate']);

        $settings->update([
            'drip_mode' => $validated['drip_mode'],
            'drip_config' => [
                'days_after_enrollment' => $validated['days_after_enrollment'] ?? 0,
                'locked_message' => $validated['locked_message'] ?? 'This lesson is locked. Complete previous content to unlock.',
                'timezone' => $validated['timezone'] ?? config('app.timezone'),
            ],
        ]);
    }

    protected function updateLearnerConfigurations(Request $request, Course $course, CourseSetting $settings): void
    {
        $validated = $request->validate([
            'min_video_watch_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'max_completion_days' => ['nullable', 'integer', 'min:1'],
            'failed_attempt_limit' => ['nullable', 'integer', 'min:1'],
        ]);

        $settings->update([
            'learner_config' => [
                'request_completion' => $request->boolean('request_completion'),
                'mark_complete' => $request->boolean('mark_complete', true),
                'require_quiz' => $request->boolean('require_quiz'),
                'require_assignment' => $request->boolean('require_assignment'),
                'restrict_skipping' => $request->boolean('restrict_skipping'),
                'restrict_seeking' => $request->boolean('restrict_seeking'),
                'min_video_watch_percent' => $validated['min_video_watch_percent'] ?? 80,
                'resume_last_position' => $request->boolean('resume_last_position', true),
                'downloadable_resources' => $request->boolean('downloadable_resources', true),
                'lesson_comments' => $request->boolean('lesson_comments'),
                'learner_notes' => $request->boolean('learner_notes', true),
                'max_completion_days' => $validated['max_completion_days'] ?? null,
                'failed_attempt_limit' => $validated['failed_attempt_limit'] ?? null,
            ],
        ]);
    }

    protected function updateLearningPath(Request $request, Course $course, CourseSetting $settings): void
    {
        $validated = $request->validate([
            'min_section_progress' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $enabled = $request->boolean('learning_path_enabled');
        if (! $enabled && $course->enrollments()->where('status', 'active')->exists() && ! $request->boolean('confirm_disable')) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'confirm_disable' => 'Confirm disabling learning path while learners are enrolled.',
            ]);
        }

        $settings->update([
            'learning_path_enabled' => $enabled,
            'learning_path_config' => [
                'sequential' => $request->boolean('sequential', true),
                'lesson_lock' => $request->boolean('lesson_lock', true),
                'section_lock' => $request->boolean('section_lock'),
                'unlock_after_completion' => $request->boolean('unlock_after_completion', true),
                'unlock_after_quiz' => $request->boolean('unlock_after_quiz'),
                'unlock_after_assignment' => $request->boolean('unlock_after_assignment'),
                'min_section_progress' => $validated['min_section_progress'] ?? 100,
                'allow_optional' => $request->boolean('allow_optional', true),
            ],
        ]);
    }

    /* -------------------- helpers -------------------- */

    protected function ensureSettings(Course $course): CourseSetting
    {
        return $course->settings()->firstOrCreate([], [
            'selling_platforms' => ['all'],
            'permissions' => [
                'allow_manual_enrollment' => true,
                'allow_batch_enrollment' => true,
            ],
        ]);
    }

    protected function audit(Course $course, string $section, string $action, array $before, array $after): void
    {
        CourseSettingAuditLog::create([
            'course_id' => $course->id,
            'section' => $section,
            'action' => $action,
            'user_id' => Auth::id(),
            'before' => $before ?: null,
            'after' => $after ?: null,
            'ip_address' => request()->ip(),
        ]);
    }

    protected function publishRequirements(Course $course): array
    {
        $missing = [];
        if (blank($course->title)) {
            $missing[] = 'Course title';
        }
        if (blank($course->description)) {
            $missing[] = 'Course description';
        }
        if (! $course->thumbnail) {
            $missing[] = 'Course thumbnail / branding image';
        }
        if ($course->sections()->count() === 0) {
            $missing[] = 'At least one curriculum section';
        }
        if ($course->lessons()->count() === 0) {
            $missing[] = 'At least one lesson';
        }

        return $missing;
    }

    protected function trashBlockers(Course $course): array
    {
        $blockers = [];
        $activePurchases = $course->enrollments()->where('status', 'active')->where('access_type', 'paid')->count();
        if ($activePurchases > 0) {
            $blockers[] = "{$activePurchases} active paid enrollment(s)";
        }
        $activeBatches = $course->batches()->whereIn('status', ['upcoming', 'active'])->count();
        if ($activeBatches > 0) {
            $blockers[] = "{$activeBatches} active/upcoming batch(es)";
        }

        return $blockers;
    }

    protected function associations(Course $course): array
    {
        return [
            [
                'key' => 'products',
                'title' => 'Products',
                'description' => 'View products this course is associated with.',
                'count' => 1,
                'url' => route('admin.courses.show', $course),
            ],
            [
                'key' => 'segments',
                'title' => 'Segments',
                'description' => 'View segments this course is associated with.',
                'count' => method_exists($course, 'segments') ? $course->segments()->count() : 0,
                'url' => route('admin.segments.index'),
            ],
            [
                'key' => 'categories',
                'title' => 'Categories',
                'description' => 'Primary category linked to this course.',
                'count' => $course->category_id ? 1 : 0,
                'url' => route('admin.categories.index'),
            ],
            [
                'key' => 'bundles',
                'title' => 'Bundles',
                'description' => 'Bundles that include this course.',
                'count' => Bundle::whereHas('courses', fn ($q) => $q->where('courses.id', $course->id))->count(),
                'url' => route('admin.bundles.index'),
            ],
            [
                'key' => 'pricing_plans',
                'title' => 'Pricing plans',
                'description' => 'Pricing plans configured for this course.',
                'count' => $course->pricingPlans()->count(),
                'url' => route('admin.courses.settings.show', [$course, 'pricing-plans']),
            ],
            [
                'key' => 'certificates',
                'title' => 'Certificates',
                'description' => 'Certificates issued for this course.',
                'count' => Certificate::where('course_id', $course->id)->count(),
                'url' => route('admin.certificates.index'),
            ],
            [
                'key' => 'instructors',
                'title' => 'Instructors',
                'description' => 'Instructors assigned to this course.',
                'count' => $course->instructors()->count(),
                'url' => route('admin.courses.settings.show', [$course, 'instructors']),
            ],
            [
                'key' => 'groups',
                'title' => 'Learner groups',
                'description' => 'Groups linked to this course.',
                'count' => Group::whereHas('courses', fn ($q) => $q->where('courses.id', $course->id))->count(),
                'url' => route('admin.groups.index'),
            ],
        ];
    }

    protected function panelStatuses(Course $course, CourseSetting $settings): array
    {
        return [
            'branding' => $course->thumbnail ? 'Configured' : 'Not set',
            'seo' => $course->seo_title ? 'Configured' : 'Basic',
            'tags' => $course->tags()->count().' tags',
            'faqs' => $course->faqs()->count().' FAQs',
            'instructors' => $course->instructors()->count().' instructors',
            'pricing_plans' => $course->pricingPlans()->count().' plans',
            'android' => data_get($settings->android_pricing, 'enabled') ? 'Enabled' : 'Optional',
            'ios' => data_get($settings->ios_pricing, 'enabled') ? 'Enabled' : 'Optional',
            'permissions' => ucfirst($settings->access_visibility ?? 'public'),
            'reviews' => $settings->reviews_enabled ? 'Enabled' : 'Disabled',
            'discussions' => $settings->discussion_enabled ? 'Enabled' : 'Disabled',
            'leaderboard' => $settings->leaderboard_enabled ? 'Enabled' : 'Disabled',
            'certificates' => $settings->certificate_enabled ? 'Enabled' : 'Disabled',
            'drip' => ucfirst(str_replace('_', ' ', $settings->drip_mode ?? 'immediate')),
            'learner' => 'Configured',
            'learning_path' => $settings->learning_path_enabled ? 'Enabled' : 'Disabled',
            'publish' => ucfirst($course->status),
            'trash' => $course->trashed() ? 'In trash' : 'Active',
            'associated' => collect($this->associations($course))->sum('count').' links',
            'remove_learners' => $course->enrollments()->where('status', 'active')->count().' active',
        ];
    }
}
