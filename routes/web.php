<?php

use App\Http\Controllers\Admin\BundleController;
use App\Http\Controllers\Admin\CheckoutConsentController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\SubAdminController;
use App\Http\Controllers\Admin\SubAdminWizardController;
use App\Http\Controllers\Admin\BatchController as AdminBatchController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\CompanyPageContentController;
use App\Http\Controllers\Admin\CompanyProfileController;
use App\Http\Controllers\Admin\CommunityController as AdminCommunityController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DiscussionController;
use App\Http\Controllers\Admin\InstructorController;
use App\Http\Controllers\Admin\LearnerController;
use App\Http\Controllers\Admin\MarketingController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ClassificationController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\WebsiteSectionController;
use App\Http\Controllers\Admin\LiveClassController;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\QuestionPoolController;
use App\Http\Controllers\Admin\CustomProductController;
use App\Http\Controllers\Admin\WebinarController;
use App\Http\Controllers\Admin\PodcastController;
use App\Http\Controllers\Admin\EbookController;
use App\Http\Controllers\Admin\MoreProductsController;
use App\Http\Controllers\Admin\MockTestController;
use App\Http\Controllers\Admin\PollController;
use App\Http\Controllers\Admin\ProductModuleController;
use App\Http\Controllers\Admin\TestSeriesController;
use App\Http\Controllers\Admin\TrackController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\InsightController;
use App\Http\Controllers\Admin\ResourceController;
use App\Http\Controllers\Admin\SegmentController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\LegalDocumentController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SidebarSettingsController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\StudentAuthController;
use App\Http\Controllers\Instructor\DashboardController as InstructorDashboardController;
use App\Http\Controllers\Learner\CommunityController as LearnerCommunityController;
use App\Http\Controllers\Learner\CourseController as LearnerCourseController;
use App\Http\Controllers\Learner\DashboardController as LearnerDashboardController;
use App\Http\Controllers\Platform\PlatformActivityController;
use App\Http\Controllers\Platform\PlatformCompanyController;
use App\Http\Controllers\Platform\PlatformDashboardController;
use App\Http\Controllers\Platform\PlatformSettingController;
use App\Http\Controllers\Platform\PlatformWebsiteContentController;
use App\Http\Controllers\Platform\PlatformSignupFormController;
use App\Http\Controllers\Platform\PlatformProductPageController;
use App\Http\Controllers\Platform\PlatformSolutionPageController;
use App\Http\Controllers\Platform\PlatformCustomerPageController;
use App\Http\Controllers\Platform\PlatformResourcePageController;
use App\Http\Controllers\Platform\PlatformBlogController;
use App\Http\Controllers\Platform\PlatformSubscriptionPackageController;
use App\Http\Controllers\Platform\PlatformUserController;
use App\Http\Controllers\Admin\UtilitiesController;
use App\Http\Controllers\Auth\SignupController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\CompanyDirectoryController;
use App\Http\Controllers\WebsiteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Marketing website (Kingster HTML + Learnyst content)
| Assets: public/website  |  Views: resources/views/website
|--------------------------------------------------------------------------
*/
Route::get('/', [WebsiteController::class, 'home'])->name('home');
Route::get('/products/{slug}', [WebsiteController::class, 'product'])->name('website.product');
Route::get('/solutions/{slug}', [WebsiteController::class, 'solution'])->name('website.solution');
Route::get('/customers/{slug}', [WebsiteController::class, 'customer'])->name('website.customer');
Route::get('/companies', [CompanyDirectoryController::class, 'index'])->name('website.companies.index');
Route::get('/companies/{slug}', [CompanyDirectoryController::class, 'show'])->name('website.companies.show');
Route::get('/companies/{slug}/blogs/{blogSlug}', [CompanyDirectoryController::class, 'blog'])->name('website.companies.blog');
Route::post('/companies/{slug}/reviews', [CompanyDirectoryController::class, 'storeReview'])->name('website.companies.reviews.store');
Route::post('/companies/{slug}/enquiries', [CompanyDirectoryController::class, 'storeEnquiry'])->name('website.companies.enquiries.store');
Route::get('/comparison/{slug}', [WebsiteController::class, 'comparison'])->name('website.comparison');
Route::get('/help-center', [WebsiteController::class, 'helpCenter'])->name('website.help-center');
Route::get('/whats-new', [WebsiteController::class, 'whatsNew'])->name('website.whats-new');
Route::get('/blogs', [WebsiteController::class, 'blogs'])->name('website.blogs');
Route::get('/blogs/{slug}', [WebsiteController::class, 'blogShow'])->name('website.blog.show');
Route::get('/pricing', [WebsiteController::class, 'pricing'])->name('website.pricing');
Route::get('/{slug}', [WebsiteController::class, 'page'])
    ->where('slug', 'about-us|product-demo|drm-security|corporate-lms|ai|careers|privacy-policy|terms-and-conditions|support-migration|guides')
    ->name('website.page');

Route::get('/home-lms', [PublicController::class, 'home'])->name('home.lms');
Route::get('/courses', [PublicController::class, 'courses'])->name('public.courses');
Route::get('/courses/{course:slug}', [PublicController::class, 'courseShow'])->name('public.course');
Route::post('/courses/{course:slug}/reviews', [PublicController::class, 'storeCourseReview'])->name('public.course.reviews.store');
Route::post('/courses/{course:slug}/enquiries', [PublicController::class, 'storeCourseEnquiry'])->name('public.course.enquiries.store');
Route::middleware(['auth', 'role:learner'])->group(function () {
    Route::post('/courses/{course:slug}/checkout', [\App\Http\Controllers\CourseCheckoutController::class, 'start'])->name('courses.checkout.start');
    Route::post('/courses/checkout/complete', [\App\Http\Controllers\CourseCheckoutController::class, 'complete'])->name('courses.checkout.complete');
});
Route::post('/leads', [PublicController::class, 'captureLead'])->name('leads.capture');
Route::get('/verify-certificate', [CertificateController::class, 'verify'])->name('certificates.verify');

// Signup is public so "Get Started" always opens (not blocked by guest redirect when already logged in).
Route::get('/signup/{step?}', [SignupController::class, 'show'])->name('signup.show');
Route::post('/signup/account', [SignupController::class, 'storeAccount'])->name('signup.account');
Route::post('/signup/company', [SignupController::class, 'storeCompany'])->name('signup.company');
Route::post('/signup/business-type', [SignupController::class, 'storeBusinessType'])->name('signup.business_type');
Route::post('/signup/teach', [SignupController::class, 'storeTeach'])->name('signup.teach');
Route::post('/signup/goal', [SignupController::class, 'storeGoal'])->name('signup.goal');
Route::post('/signup/content-ready', [SignupController::class, 'storeContentReady'])->name('signup.content_ready');
Route::post('/signup/audience', [SignupController::class, 'storeAudience'])->name('signup.audience');
Route::post('/signup/source', [SignupController::class, 'storeSource'])->name('signup.source');
Route::post('/signup/resend', [SignupController::class, 'resendVerification'])->name('signup.resend');
Route::post('/signup/verified', [SignupController::class, 'markVerifiedAndLogin'])->name('signup.verified');

Route::middleware('guest')->group(function () {
    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/admin/login', [LoginController::class, 'showPlatformLoginForm'])->name('platform.login');
    Route::post('/admin/login', [LoginController::class, 'loginPlatform'])->name('platform.login.submit');
    Route::get('/student/login', [StudentAuthController::class, 'showLoginForm'])->name('student.login');
    Route::post('/student/login', [StudentAuthController::class, 'login'])->name('student.login.submit');
    Route::get('/student/register', [StudentAuthController::class, 'showRegisterForm'])->name('student.register');
    Route::post('/student/register', [StudentAuthController::class, 'register'])->name('student.register.submit');
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

Route::prefix('company')->name('admin.')->middleware(['auth', 'role:admin,sub-admin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('courses', AdminCourseController::class);
    Route::get('courses/{course}/builder', [AdminCourseController::class, 'builder'])->name('courses.builder');
    Route::post('courses/{course}/duplicate', [AdminCourseController::class, 'duplicate'])->name('courses.duplicate');
    Route::post('courses/{course}/publish', [AdminCourseController::class, 'publish'])->name('courses.publish');
    Route::post('courses/{course}/unpublish', [AdminCourseController::class, 'unpublish'])->name('courses.unpublish');

    Route::get('courses/{course}/settings', [\App\Http\Controllers\Admin\CourseSettingsController::class, 'hub'])->name('courses.settings.hub');
    Route::get('courses/{course}/settings/{panel}', [\App\Http\Controllers\Admin\CourseSettingsController::class, 'show'])->name('courses.settings.show');
    Route::put('courses/{course}/settings/{panel}', [\App\Http\Controllers\Admin\CourseSettingsController::class, 'update'])->name('courses.settings.update');
    Route::post('courses/{course}/settings/publish', [\App\Http\Controllers\Admin\CourseSettingsController::class, 'publish'])->name('courses.settings.publish');
    Route::post('courses/{course}/settings/trash', [\App\Http\Controllers\Admin\CourseSettingsController::class, 'trash'])->name('courses.settings.trash');
    Route::post('courses/{course}/settings/restore', [\App\Http\Controllers\Admin\CourseSettingsController::class, 'restore'])->name('courses.settings.restore');
    Route::post('courses/{course}/settings/remove-learners', [\App\Http\Controllers\Admin\CourseSettingsController::class, 'removeLearners'])->name('courses.settings.remove-learners');
    Route::post('courses/{course}/settings/removals/{removal}/restore', [\App\Http\Controllers\Admin\CourseSettingsController::class, 'restoreLearner'])->name('courses.settings.removals.restore');
    Route::post('courses/{course}/settings/pricing-plans', [\App\Http\Controllers\Admin\CourseSettingsController::class, 'storePricingPlan'])->name('courses.settings.pricing-plans.store');
    Route::post('courses/{course}/settings/pricing-plans/{plan}/status', [\App\Http\Controllers\Admin\CourseSettingsController::class, 'updatePricingPlanStatus'])->name('courses.settings.pricing-plans.status');
    Route::post('courses/{course}/settings/pricing-plans/{plan}/duplicate', [\App\Http\Controllers\Admin\CourseSettingsController::class, 'duplicatePricingPlan'])->name('courses.settings.pricing-plans.duplicate');
    Route::delete('courses/{course}/settings/pricing-plans/{plan}', [\App\Http\Controllers\Admin\CourseSettingsController::class, 'destroyPricingPlan'])->name('courses.settings.pricing-plans.destroy');
    Route::post('courses/{course}/settings/faqs', [\App\Http\Controllers\Admin\CourseSettingsController::class, 'storeFaq'])->name('courses.settings.faqs.store');
    Route::delete('courses/{course}/settings/faqs/{faq}', [\App\Http\Controllers\Admin\CourseSettingsController::class, 'destroyFaq'])->name('courses.settings.faqs.destroy');
    Route::post('courses/{course}/settings/certificate-criteria', [\App\Http\Controllers\Admin\CourseSettingsController::class, 'storeCertificateCriterion'])->name('courses.settings.criteria.store');
    Route::delete('courses/{course}/settings/certificate-criteria/{criterion}', [\App\Http\Controllers\Admin\CourseSettingsController::class, 'destroyCertificateCriterion'])->name('courses.settings.criteria.destroy');

    Route::post('courses/{course}/sections', [AdminCourseController::class, 'storeSection'])->name('courses.sections.store');
    Route::post('courses/{course}/sections/reorder', [AdminCourseController::class, 'reorderSections'])->name('courses.sections.reorder');
    Route::put('sections/{section}', [AdminCourseController::class, 'updateSection'])->name('sections.update');
    Route::post('sections/{section}/lessons', [AdminCourseController::class, 'storeLesson'])->name('sections.lessons.store');
    Route::post('sections/{section}/lessons/reorder', [AdminCourseController::class, 'reorderLessons'])->name('sections.lessons.reorder');
    Route::delete('sections/{section}', [AdminCourseController::class, 'destroySection'])->name('sections.destroy');
    Route::delete('lessons/{lesson}', [AdminCourseController::class, 'destroyLesson'])->name('lessons.destroy');

    Route::get('lessons/{lesson}/edit', [LessonController::class, 'edit'])->name('lessons.edit');
    Route::put('lessons/{lesson}', [LessonController::class, 'update'])->name('lessons.update');
    Route::put('lessons/{lesson}/settings', [LessonController::class, 'updateSettings'])->name('lessons.settings.update');
    Route::post('lessons/{lesson}/media', [LessonController::class, 'uploadMedia'])->name('lessons.media.upload');
    Route::post('lessons/{lesson}/attachments', [LessonController::class, 'storeAttachment'])->name('lessons.attachments.store');
    Route::delete('attachments/{attachment}', [LessonController::class, 'destroyAttachment'])->name('attachments.destroy');
    Route::put('lessons/{lesson}/live-class', [LessonController::class, 'updateLiveClass'])->name('lessons.live-class.update');
    Route::delete('lessons/{lesson}/remove', [LessonController::class, 'destroy'])->name('lessons.remove');

    Route::resource('bundles', BundleController::class);

    Route::resource('mock-tests', MockTestController::class)->except(['show']);
    Route::resource('test-series', TestSeriesController::class)->except(['show']);
    Route::resource('polls', PollController::class)->except(['show', 'edit', 'update']);
    Route::resource('tracks', TrackController::class)->except(['show', 'edit', 'update']);

    Route::get('more-products', [MoreProductsController::class, 'index'])->name('more-products.index');
    Route::resource('ebooks', EbookController::class)->except(['show', 'edit', 'update']);
    Route::resource('podcasts', PodcastController::class)->except(['show', 'edit', 'update']);
    Route::resource('webinars', WebinarController::class)->except(['show', 'edit', 'update']);
    Route::resource('custom-products', CustomProductController::class)->except(['show', 'edit', 'update']);
    Route::resource('question-pools', QuestionPoolController::class)->except(['show', 'edit', 'update']);
    Route::resource('questions', QuestionController::class)->only(['index', 'destroy']);

    Route::get('classification', [ClassificationController::class, 'index'])->name('classification.index');
    Route::get('tags', [TagController::class, 'index'])->name('tags.index');
    Route::get('tags/create', [TagController::class, 'create'])->name('tags.create');
    Route::post('tags', [TagController::class, 'store'])->name('tags.store');
    Route::delete('tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');

    Route::get('utilities', [UtilitiesController::class, 'index'])->name('utilities.index');
    Route::get('utilities/copy-product', [UtilitiesController::class, 'copyProduct'])->name('utilities.copy-product');
    Route::get('utilities/copy-product/course', [UtilitiesController::class, 'copyCourse'])->name('utilities.copy-course');
    Route::get('utilities/copy-product/mock-test', [UtilitiesController::class, 'copyMockTest'])->name('utilities.copy-mock-test');
    Route::get('utilities/copy-product/test-series', [UtilitiesController::class, 'copyTestSeries'])->name('utilities.copy-test-series');

    Route::resource('website-sections', WebsiteSectionController::class)->except(['show']);
    Route::get('website-sections-preview', [WebsiteSectionController::class, 'preview'])->name('website-sections.preview');

    Route::resource('live-classes', LiveClassController::class)->except(['show']);
    Route::get('quizzes/{lesson}/edit', [QuizController::class, 'edit'])->name('quizzes.edit');
    Route::put('quizzes/{lesson}', [QuizController::class, 'update'])->name('quizzes.update');
    Route::delete('quizzes/{lesson}', [QuizController::class, 'destroy'])->name('quizzes.destroy');
    Route::resource('quizzes', QuizController::class)->only(['index', 'create', 'store']);
    Route::get('assignments/{lesson}/edit', [AssignmentController::class, 'edit'])->name('assignments.edit');
    Route::put('assignments/{lesson}', [AssignmentController::class, 'update'])->name('assignments.update');
    Route::delete('assignments/{lesson}', [AssignmentController::class, 'destroy'])->name('assignments.destroy');
    Route::resource('assignments', AssignmentController::class)->only(['index', 'create', 'store']);

    foreach ([
        'code' => 'code.index',
    ] as $uri => $routeName) {
        Route::get($uri, fn () => app(ProductModuleController::class)->show($uri))->name($routeName);
    }

    Route::resource('learners', LearnerController::class);
    Route::post('learners/{learner}/enroll', [LearnerController::class, 'enroll'])->name('learners.enroll');
    Route::delete('enrollments/{enrollment}', [LearnerController::class, 'revokeEnrollment'])->name('enrollments.revoke');
    Route::get('learners-export', [LearnerController::class, 'export'])->name('learners.export');
    Route::post('learners-import', [LearnerController::class, 'import'])->name('learners.import');

    Route::get('enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index');
    Route::post('enrollments', [EnrollmentController::class, 'store'])->name('enrollments.store');
    Route::put('enrollments/{enrollment}', [EnrollmentController::class, 'update'])->name('enrollments.update');
    Route::delete('enrollments/{enrollment}', [EnrollmentController::class, 'destroy'])->name('enrollments.destroy');
    Route::get('enrollments/history/{learner}', [EnrollmentController::class, 'history'])->name('enrollments.history');

    Route::resource('groups', GroupController::class);
    Route::post('groups/{group}/learners', [GroupController::class, 'addLearner'])->name('groups.learners.add');
    Route::delete('groups/{group}/learners/{user}', [GroupController::class, 'removeLearner'])->name('groups.learners.remove');
    Route::post('groups/{group}/courses', [GroupController::class, 'assignCourse'])->name('groups.courses.assign');
    Route::delete('groups/{group}/courses/{course}', [GroupController::class, 'removeCourse'])->name('groups.courses.remove');

    Route::resource('contacts', ContactController::class)->except(['show']);
    Route::resource('legal-documents', LegalDocumentController::class)->except(['show']);

    Route::resource('orders', OrderController::class)->except(['edit', 'update', 'destroy']);
    Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::post('orders/{order}/refund', [OrderController::class, 'refund'])->name('orders.refund');

    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');

    Route::resource('checkout-consents', CheckoutConsentController::class)->except(['show']);
    Route::get('checkout-consents-report', [CheckoutConsentController::class, 'report'])->name('checkout-consents.report');

    Route::resource('batches', AdminBatchController::class);
    Route::post('batches/{batch}/learners', [AdminBatchController::class, 'addLearner'])->name('batches.learners.add');
    Route::delete('batches/{batch}/learners/{user}', [AdminBatchController::class, 'removeLearner'])->name('batches.learners.remove');

    Route::resource('instructors', InstructorController::class);
    Route::post('instructors/{instructor}/courses', [InstructorController::class, 'assignCourse'])->name('instructors.courses.assign');
    Route::delete('instructors/{instructor}/courses/{course}', [InstructorController::class, 'removeCourse'])->name('instructors.courses.remove');
    Route::post('instructors/{instructor}/batches', [InstructorController::class, 'assignBatch'])->name('instructors.batches.assign');

    Route::get('sub-admins', [SubAdminController::class, 'index'])->name('sub-admins.index');
    Route::get('sub-admins/wizard', [SubAdminWizardController::class, 'create'])->name('sub-admins.wizard');
    Route::get('sub-admins/wizard/step/{step}', [SubAdminWizardController::class, 'step'])->whereNumber('step')->name('sub-admins.wizard.step');
    Route::post('sub-admins/wizard/step/{step}', [SubAdminWizardController::class, 'storeStep'])->whereNumber('step')->name('sub-admins.wizard.store');
    Route::post('sub-admins/wizard/finish', [SubAdminWizardController::class, 'finish'])->name('sub-admins.wizard.finish');
    Route::get('sub-admins/{subAdmin}', [SubAdminController::class, 'show'])->name('sub-admins.show');
    Route::get('sub-admins/{subAdmin}/edit', [SubAdminController::class, 'edit'])->name('sub-admins.edit');
    Route::put('sub-admins/{subAdmin}', [SubAdminController::class, 'update'])->name('sub-admins.update');
    Route::delete('sub-admins/{subAdmin}', [SubAdminController::class, 'destroy'])->name('sub-admins.destroy');
    Route::post('sub-admins/{subAdmin}/toggle', [SubAdminController::class, 'toggleStatus'])->name('sub-admins.toggle');

    Route::get('roles', [RolePermissionController::class, 'roles'])->name('roles.index');
    Route::post('roles', [RolePermissionController::class, 'storeRole'])->name('roles.store');
    Route::get('roles/{role}/edit', [RolePermissionController::class, 'editRole'])->name('roles.edit');
    Route::put('roles/{role}', [RolePermissionController::class, 'updateRole'])->name('roles.update');
    Route::delete('roles/{role}', [RolePermissionController::class, 'destroyRole'])->name('roles.destroy');
    Route::get('permissions', [RolePermissionController::class, 'permissions'])->name('permissions.index');
    Route::post('permissions/seed', [RolePermissionController::class, 'seedPermissions'])->name('permissions.seed');

    Route::resource('communities', AdminCommunityController::class);
    Route::post('communities/{community}/members', [AdminCommunityController::class, 'addMember'])->name('communities.members.add');
    Route::post('communities/{community}/posts', [AdminCommunityController::class, 'storePost'])->name('communities.posts.store');
    Route::delete('community-posts/{post}', [AdminCommunityController::class, 'destroyPost'])->name('communities.posts.destroy');

    Route::get('discussions', [DiscussionController::class, 'index'])->name('discussions.index');
    Route::get('discussions/{discussion}', [DiscussionController::class, 'show'])->name('discussions.show');
    Route::post('discussions/{discussion}/lock', [DiscussionController::class, 'lock'])->name('discussions.lock');
    Route::delete('discussions/{discussion}', [DiscussionController::class, 'destroy'])->name('discussions.destroy');

    Route::get('certificates', [CertificateController::class, 'index'])->name('certificates.index');
    Route::get('certificates/templates', [CertificateController::class, 'templates'])->name('certificates.templates');
    Route::post('certificates/templates', [CertificateController::class, 'storeTemplate'])->name('certificates.templates.store');
    Route::post('certificates/issue', [CertificateController::class, 'issue'])->name('certificates.issue');

    Route::get('marketing/coupons', [MarketingController::class, 'coupons'])->name('marketing.coupons');
    Route::post('marketing/coupons', [MarketingController::class, 'storeCoupon'])->name('marketing.coupons.store');
    Route::delete('marketing/coupons/{coupon}', [MarketingController::class, 'destroyCoupon'])->name('marketing.coupons.destroy');
    Route::get('marketing/campaigns', [MarketingController::class, 'campaigns'])->name('marketing.campaigns');
    Route::post('marketing/campaigns', [MarketingController::class, 'storeCampaign'])->name('marketing.campaigns.store');
    Route::get('marketing/leads', [MarketingController::class, 'leads'])->name('marketing.leads');
    Route::post('marketing/leads', [MarketingController::class, 'storeLead'])->name('marketing.leads.store');

    Route::resource('resources', ResourceController::class)->except(['show']);
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('segments', [SegmentController::class, 'index'])->name('segments.index');
    Route::get('segments/create', [SegmentController::class, 'create'])->name('segments.create');
    Route::post('segments', [SegmentController::class, 'store'])->name('segments.store');
    Route::get('segments/{segment}', [SegmentController::class, 'show'])->name('segments.show');
    Route::put('segments/{segment}', [SegmentController::class, 'update'])->name('segments.update');
    Route::delete('segments/{segment}', [SegmentController::class, 'destroy'])->name('segments.destroy');
    Route::post('segments/{segment}/learners', [SegmentController::class, 'assignLearner'])->name('segments.learners.assign');
    Route::post('segments/{segment}/courses', [SegmentController::class, 'assignCourse'])->name('segments.courses.assign');

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/sales', [ReportController::class, 'salesIndex'])->name('reports.sales.index');
    Route::get('reports/sales/orders', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('reports/sales/product', [ReportController::class, 'productSales'])->name('reports.product-sales');
    Route::get('reports/sales/referral-wallet', [ReportController::class, 'referralWallet'])->name('reports.referral-wallet');
    Route::get('reports/sales/affiliate-products', [ReportController::class, 'affiliateProducts'])->name('reports.affiliate-products');
    Route::get('reports/sales/affiliates', [ReportController::class, 'affiliates'])->name('reports.affiliates');
    Route::get('reports/sales/coupons', [ReportController::class, 'coupons'])->name('reports.coupons');
    Route::get('reports/sales/broadcast', [ReportController::class, 'broadcastMessages'])->name('reports.broadcast');
    Route::get('reports/learners', [ReportController::class, 'learners'])->name('reports.learners');
    Route::get('reports/learners/{user}', [ReportController::class, 'learnerProfile'])->name('reports.learner-profile');
    Route::get('reports/courses', [ReportController::class, 'courses'])->name('reports.courses');
    Route::get('reports/enrollments', [ReportController::class, 'enrollments'])->name('reports.enrollments');
    Route::get('reports/transactions', [ReportController::class, 'transactions'])->name('reports.transactions');
    Route::get('reports/payment-gateways', [ReportController::class, 'paymentGateways'])->name('reports.payment-gateways');
    Route::get('reports/school-payouts', [ReportController::class, 'schoolPayouts'])->name('reports.school-payouts');
    Route::get('reports/progress', [ReportController::class, 'progressIndex'])->name('reports.progress.index');
    Route::get('reports/progress/{type}', [ReportController::class, 'productProgress'])->name('reports.progress.type');
    Route::get('reports/bundle-progress', [ReportController::class, 'bundleProgress'])->name('reports.bundle-progress');
    Route::get('reports/custom-product-progress', [ReportController::class, 'customProductProgress'])->name('reports.custom-product-progress');
    Route::get('reports/test-series-scores', [ReportController::class, 'testSeriesScores'])->name('reports.test-series-scores');
    Route::get('reports/bundles', [ReportController::class, 'bundles'])->name('reports.bundles');
    Route::get('reports/payments', [ReportController::class, 'payments'])->name('reports.payments');
    Route::get('reports/batches', [ReportController::class, 'batches'])->name('reports.batches');
    Route::get('reports/certificates', [ReportController::class, 'certificates'])->name('reports.certificates');
    Route::get('reports/zoom-insights', [ReportController::class, 'zoomInsights'])->name('reports.zoom-insights');
    Route::get('reports/live-class-attendance', [ReportController::class, 'liveClassAttendance'])->name('reports.live-class-attendance');
    Route::get('reports/resource-usage', [ReportController::class, 'resourceUsage'])->name('reports.resource-usage');
    Route::get('reports/super-live-lessons', [ReportController::class, 'superLiveLessons'])->name('reports.super-live-lessons');

    Route::prefix('insights')->name('insights.')->group(function () {
        Route::get('/', [InsightController::class, 'dashboard'])->name('dashboard');
        Route::get('school-vitals', [InsightController::class, 'schoolVitals'])->name('school-vitals');
        Route::get('monthly-revenue', [InsightController::class, 'monthlyRevenue'])->name('monthly-revenue');
        Route::get('active-learners', [InsightController::class, 'activeLearners'])->name('active-learners');
        Route::get('conversions', [InsightController::class, 'conversions'])->name('conversions');
        Route::get('time-spent', [InsightController::class, 'timeSpent'])->name('time-spent');
        Route::get('sales', [InsightController::class, 'salesIndex'])->name('sales.index');
        Route::get('sales/fresh-trial', [InsightController::class, 'freshTrial'])->name('sales.fresh-trial');
        Route::get('sales/upsell-trial', [InsightController::class, 'upsellTrial'])->name('sales.upsell-trial');
        Route::get('sales/renewal-trial', [InsightController::class, 'renewalTrial'])->name('sales.renewal-trial');
        Route::get('sales/free-users', [InsightController::class, 'freeUsers'])->name('sales.free-users');
        Route::get('live', [InsightController::class, 'liveIndex'])->name('live.index');
        Route::get('live/classes', [InsightController::class, 'liveClasses'])->name('live.classes');
        Route::get('live/checkout', [InsightController::class, 'checkout'])->name('live.checkout');
        Route::get('live/test-takes', [InsightController::class, 'testTakes'])->name('live.test-takes');
        Route::get('marketing', [InsightController::class, 'marketingIndex'])->name('marketing.index');
        Route::get('marketing/cta', [InsightController::class, 'ctaInsights'])->name('marketing.cta');
        Route::get('messenger', [InsightController::class, 'messengerIndex'])->name('messenger.index');
        Route::get('messenger/system-mails', [InsightController::class, 'systemMails'])->name('messenger.system-mails');
        Route::get('messenger/marketing-mails', [InsightController::class, 'marketingMails'])->name('messenger.marketing-mails');
        Route::get('messenger/push-messages', [InsightController::class, 'pushMessages'])->name('messenger.push-messages');
        Route::get('messenger/workflow-mails', [InsightController::class, 'workflowMails'])->name('messenger.workflow-mails');
        Route::get('messenger/email-delivery', [InsightController::class, 'emailDelivery'])->name('messenger.email-delivery');
        Route::get('messenger/bounces-complaints', [InsightController::class, 'bouncesComplaints'])->name('messenger.bounces-complaints');
        Route::get('messenger/whatsapp-messages', [InsightController::class, 'whatsappMessages'])->name('messenger.whatsapp-messages');
        Route::get('messenger/whatsapp-workflow', [InsightController::class, 'whatsappWorkflow'])->name('messenger.whatsapp-workflow');
    });

    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('settings/logo', [SettingController::class, 'uploadLogo'])->name('settings.logo');
    Route::get('settings/social', [SettingController::class, 'socialLinks'])->name('settings.social');
    Route::put('settings/social', [SettingController::class, 'updateSocialLinks'])->name('settings.social.update');
    Route::get('settings/sidebar', [SidebarSettingsController::class, 'edit'])->name('settings.sidebar');
    Route::put('settings/sidebar', [SidebarSettingsController::class, 'update'])->name('settings.sidebar.update');
    Route::delete('settings/sidebar/reset', [SidebarSettingsController::class, 'reset'])->name('settings.sidebar.reset');
    Route::get('profile', [CompanyProfileController::class, 'edit'])->name('company-profile.edit');
    Route::put('profile', [CompanyProfileController::class, 'update'])->name('company-profile.update');

    Route::get('page/testimonials', [CompanyPageContentController::class, 'testimonialsIndex'])->name('company-page.testimonials');
    Route::post('page/testimonials', [CompanyPageContentController::class, 'testimonialsStore'])->name('company-page.testimonials.store');
    Route::put('page/testimonials/{testimonial}', [CompanyPageContentController::class, 'testimonialsUpdate'])->name('company-page.testimonials.update');
    Route::post('page/testimonials/{testimonial}/toggle', [CompanyPageContentController::class, 'testimonialsToggle'])->name('company-page.testimonials.toggle');
    Route::delete('page/testimonials/{testimonial}', [CompanyPageContentController::class, 'testimonialsDestroy'])->name('company-page.testimonials.destroy');

    Route::get('page/reviews', [CompanyPageContentController::class, 'reviewsIndex'])->name('company-page.reviews');
    Route::post('page/reviews/{review}/approve', [CompanyPageContentController::class, 'reviewsApprove'])->name('company-page.reviews.approve');
    Route::post('page/reviews/{review}/reject', [CompanyPageContentController::class, 'reviewsReject'])->name('company-page.reviews.reject');
    Route::delete('page/reviews/{review}', [CompanyPageContentController::class, 'reviewsDestroy'])->name('company-page.reviews.destroy');

    Route::get('page/enquiries', [CompanyPageContentController::class, 'enquiriesIndex'])->name('company-page.enquiries');
    Route::post('page/enquiries/{enquiry}/{status}', [CompanyPageContentController::class, 'enquiriesMark'])->name('company-page.enquiries.mark');
    Route::delete('page/enquiries/{enquiry}', [CompanyPageContentController::class, 'enquiriesDestroy'])->name('company-page.enquiries.destroy');

    Route::get('page/gallery', [CompanyPageContentController::class, 'galleryIndex'])->name('company-page.gallery');
    Route::post('page/gallery', [CompanyPageContentController::class, 'galleryStore'])->name('company-page.gallery.store');
    Route::put('page/gallery/{gallery}', [CompanyPageContentController::class, 'galleryUpdate'])->name('company-page.gallery.update');
    Route::post('page/gallery/{gallery}/toggle', [CompanyPageContentController::class, 'galleryToggle'])->name('company-page.gallery.toggle');
    Route::delete('page/gallery/{gallery}', [CompanyPageContentController::class, 'galleryDestroy'])->name('company-page.gallery.destroy');

    Route::get('page/videos', [CompanyPageContentController::class, 'videosIndex'])->name('company-page.videos');
    Route::post('page/videos', [CompanyPageContentController::class, 'videosStore'])->name('company-page.videos.store');
    Route::put('page/videos/{video}', [CompanyPageContentController::class, 'videosUpdate'])->name('company-page.videos.update');
    Route::delete('page/videos/{video}', [CompanyPageContentController::class, 'videosDestroy'])->name('company-page.videos.destroy');

    Route::get('page/blogs', [CompanyPageContentController::class, 'blogsIndex'])->name('company-page.blogs');
    Route::post('page/blogs', [CompanyPageContentController::class, 'blogsStore'])->name('company-page.blogs.store');
    Route::put('page/blogs/{blog}', [CompanyPageContentController::class, 'blogsUpdate'])->name('company-page.blogs.update');
    Route::delete('page/blogs/{blog}', [CompanyPageContentController::class, 'blogsDestroy'])->name('company-page.blogs.destroy');

    Route::get('page/team', [CompanyPageContentController::class, 'teamIndex'])->name('company-page.team');
    Route::post('page/team', [CompanyPageContentController::class, 'teamStore'])->name('company-page.team.store');
    Route::put('page/team/{member}', [CompanyPageContentController::class, 'teamUpdate'])->name('company-page.team.update');
    Route::delete('page/team/{member}', [CompanyPageContentController::class, 'teamDestroy'])->name('company-page.team.destroy');
});

Route::prefix('admin')->name('platform.')->middleware(['auth', 'role:super-admin'])->group(function () {
    Route::get('/', [PlatformDashboardController::class, 'index'])->name('dashboard');
    Route::get('companies', [PlatformCompanyController::class, 'index'])->name('companies.index');
    Route::get('users', [PlatformUserController::class, 'index'])->name('users.index');
    Route::get('activity-logs', [PlatformActivityController::class, 'index'])->name('activity.index');
    Route::get('website-content', [PlatformWebsiteContentController::class, 'index'])->name('website-content.index');
    Route::get('website-content/{section}', [PlatformWebsiteContentController::class, 'edit'])->name('website-content.edit');
    Route::put('website-content/{section}', [PlatformWebsiteContentController::class, 'update'])->name('website-content.update');
    Route::delete('website-content/{section}', [PlatformWebsiteContentController::class, 'reset'])->name('website-content.reset');
    Route::get('signup-form', [PlatformSignupFormController::class, 'index'])->name('signup-form.index');
    Route::get('signup-form/{signupQuestion}', [PlatformSignupFormController::class, 'edit'])->name('signup-form.edit');
    Route::put('signup-form/{signupQuestion}', [PlatformSignupFormController::class, 'update'])->name('signup-form.update');
    Route::delete('signup-form/{signupQuestion}', [PlatformSignupFormController::class, 'reset'])->name('signup-form.reset');
    Route::get('product-pages', [PlatformProductPageController::class, 'index'])->name('product-pages.index');
    Route::get('product-pages/{productSlug}', [PlatformProductPageController::class, 'edit'])->name('product-pages.edit');
    Route::put('product-pages/{productSlug}', [PlatformProductPageController::class, 'update'])->name('product-pages.update');
    Route::delete('product-pages/{productSlug}', [PlatformProductPageController::class, 'reset'])->name('product-pages.reset');
    Route::get('solution-pages', [PlatformSolutionPageController::class, 'index'])->name('solution-pages.index');
    Route::get('solution-pages/{solutionSlug}', [PlatformSolutionPageController::class, 'edit'])->name('solution-pages.edit');
    Route::put('solution-pages/{solutionSlug}', [PlatformSolutionPageController::class, 'update'])->name('solution-pages.update');
    Route::delete('solution-pages/{solutionSlug}', [PlatformSolutionPageController::class, 'reset'])->name('solution-pages.reset');
    Route::get('customer-pages', [PlatformCustomerPageController::class, 'index'])->name('customer-pages.index');
    Route::get('customer-pages/{customerSlug}', [PlatformCustomerPageController::class, 'edit'])->name('customer-pages.edit');
    Route::put('customer-pages/{customerSlug}', [PlatformCustomerPageController::class, 'update'])->name('customer-pages.update');
    Route::delete('customer-pages/{customerSlug}', [PlatformCustomerPageController::class, 'reset'])->name('customer-pages.reset');
    Route::get('resource-pages', [PlatformResourcePageController::class, 'index'])->name('resource-pages.index');
    Route::get('resource-pages/{resourceSlug}', [PlatformResourcePageController::class, 'edit'])->name('resource-pages.edit');
    Route::put('resource-pages/{resourceSlug}', [PlatformResourcePageController::class, 'update'])->name('resource-pages.update');
    Route::delete('resource-pages/{resourceSlug}', [PlatformResourcePageController::class, 'reset'])->name('resource-pages.reset');
    Route::get('blogs', [PlatformBlogController::class, 'edit'])->name('blogs.edit');
    Route::put('blogs', [PlatformBlogController::class, 'update'])->name('blogs.update');
    Route::delete('blogs', [PlatformBlogController::class, 'reset'])->name('blogs.reset');
    Route::get('subscription-packages', [PlatformSubscriptionPackageController::class, 'index'])->name('subscription-packages.index');
    Route::get('subscription-packages/create', [PlatformSubscriptionPackageController::class, 'create'])->name('subscription-packages.create');
    Route::post('subscription-packages', [PlatformSubscriptionPackageController::class, 'store'])->name('subscription-packages.store');
    Route::get('subscription-packages/{subscriptionPackage}/edit', [PlatformSubscriptionPackageController::class, 'edit'])->name('subscription-packages.edit');
    Route::put('subscription-packages/{subscriptionPackage}', [PlatformSubscriptionPackageController::class, 'update'])->name('subscription-packages.update');
    Route::post('subscription-packages/{subscriptionPackage}/toggle', [PlatformSubscriptionPackageController::class, 'toggle'])->name('subscription-packages.toggle');
    Route::delete('subscription-packages/{subscriptionPackage}', [PlatformSubscriptionPackageController::class, 'destroy'])->name('subscription-packages.destroy');
    Route::get('settings', [PlatformSettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [PlatformSettingController::class, 'update'])->name('settings.update');
});

Route::get('/admin/{path}', function (string $path) {
    return redirect('/company/' . ltrim($path, '/'), 301);
})->where('path', '.*')->middleware('auth');

Route::prefix('instructor')->name('instructor.')->middleware(['auth', 'role:instructor'])->group(function () {
    Route::get('/', [InstructorDashboardController::class, 'index'])->name('dashboard');
});

Route::prefix('learner')->name('learner.')->middleware(['auth', 'role:learner'])->group(function () {
    Route::get('/', [LearnerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/courses', [LearnerCourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course:slug}', [LearnerCourseController::class, 'show'])->name('courses.show');
    Route::get('/lessons/{lesson}', [LearnerCourseController::class, 'lesson'])->name('lessons.show');
    Route::post('/lessons/{lesson}/complete', [LearnerCourseController::class, 'complete'])->name('lessons.complete');
    Route::post('/lessons/{lesson}/incomplete', [LearnerCourseController::class, 'incomplete'])->name('lessons.incomplete');
    Route::post('/courses/{course:slug}/certificate', [LearnerCourseController::class, 'issueCertificate'])->name('courses.certificate.issue');
    Route::get('/certificates/{certificate}/download', [LearnerCourseController::class, 'downloadCertificate'])->name('certificates.download');
    Route::get('/certificates', [LearnerDashboardController::class, 'certificates'])->name('certificates');
    Route::get('/communities', [LearnerCommunityController::class, 'index'])->name('communities.index');
    Route::get('/communities/{community:slug}', [LearnerCommunityController::class, 'show'])->name('communities.show');
});
