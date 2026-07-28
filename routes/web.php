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
use App\Http\Controllers\Admin\CertificateRenewalController;
use App\Http\Controllers\Admin\AlumniController;
use App\Http\Controllers\Admin\ProctoringController;
use App\Http\Controllers\Admin\ComplianceController;
use App\Http\Controllers\Admin\NotificationCenterController;
use App\Http\Controllers\Admin\ParentLinkController;
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
use App\Http\Controllers\Admin\WalletController;
use App\Http\Controllers\Admin\ReferralController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\GstInvoiceController;
use App\Http\Controllers\Admin\AffiliateController;
use App\Http\Controllers\Admin\GamificationController;
use App\Http\Controllers\Admin\CrmController;
use App\Http\Controllers\Admin\AutomationController;
use App\Http\Controllers\Admin\LandingPageController;
use App\Http\Controllers\Admin\WebinarRegistrationController;
use App\Http\Controllers\Admin\AiCenterController;
use App\Http\Controllers\Admin\WhiteLabelController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\HrController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\PlacementController;
use App\Http\Controllers\Admin\DigitalLibraryController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\IntegrationController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SidebarSettingsController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\StudentAuthController;
use App\Http\Controllers\Instructor\DashboardController as InstructorDashboardController;
use App\Http\Controllers\Instructor\AiController as InstructorAiController;
use App\Http\Controllers\Instructor\CourseController as InstructorCourseController;
use App\Http\Controllers\Instructor\LiveClassController as InstructorLiveClassController;
use App\Http\Controllers\Instructor\AssessmentController as InstructorAssessmentController;
use App\Http\Controllers\Instructor\StudentController as InstructorStudentController;
use App\Http\Controllers\Instructor\DiscussionController as InstructorDiscussionController;
use App\Http\Controllers\Instructor\CertificateController as InstructorCertificateController;
use App\Http\Controllers\Instructor\ReportController as InstructorReportController;
use App\Http\Controllers\Admin\WebsiteBuilderController;
use App\Http\Controllers\Learner\CommunityController as LearnerCommunityController;
use App\Http\Controllers\Learner\CourseController as LearnerCourseController;
use App\Http\Controllers\Learner\CertificateRenewalController as LearnerCertificateRenewalController;
use App\Http\Controllers\Learner\DashboardController as LearnerDashboardController;
use App\Http\Controllers\Learner\WalletController as LearnerWalletController;
use App\Http\Controllers\Learner\GamificationController as LearnerGamificationController;
use App\Http\Controllers\Learner\AiAssistantController as LearnerAiAssistantController;
use App\Http\Controllers\Learner\PlacementController as LearnerPlacementController;
use App\Http\Controllers\Learner\LibraryController as LearnerLibraryController;
use App\Http\Controllers\Learner\MediaStreamController;
use App\Http\Controllers\Learner\SecurityController as LearnerSecurityController;
use App\Http\Controllers\Alumni\DashboardController as AlumniDashboardController;
use App\Http\Controllers\Parent\DashboardController as ParentDashboardController;
use App\Http\Controllers\PublicCertificateController;
use App\Http\Controllers\Platform\PlatformActivityController;
use App\Http\Controllers\Platform\PlatformSalesController;
use App\Http\Controllers\Platform\PlatformAcademicController;
use App\Http\Controllers\Platform\PlatformReportController;
use App\Http\Controllers\Platform\PlatformIntegrationsController;
use App\Http\Controllers\Platform\PlatformRoleController;
use App\Http\Controllers\Platform\PlatformSecurityController;
use App\Http\Controllers\Platform\PlatformHealthController;
use App\Http\Controllers\Platform\PlatformAnnouncementController;
use App\Http\Controllers\Platform\PlatformTicketController;
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
use App\Http\Controllers\Platform\PlatformBrandingController;
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
| Marketing website (Kingster HTML + StudyNest content)
| Assets: public/website  |  Views: resources/views/website
|--------------------------------------------------------------------------
*/
Route::get('/', [WebsiteController::class, 'home'])->name('home');
Route::get('/products/{slug}', [WebsiteController::class, 'product'])->name('website.product');
Route::get('/solutions/{slug}', [WebsiteController::class, 'solution'])->name('website.solution');
Route::get('/customers/{slug}', [WebsiteController::class, 'customer'])->name('website.customer');
Route::get('/companies', [CompanyDirectoryController::class, 'index'])->name('website.companies.index');
Route::get('/companies/{slug}', [CompanyDirectoryController::class, 'show'])->name('website.companies.show');
Route::get('/companies/{slug}/pages/{pageSlug}', [CompanyDirectoryController::class, 'page'])->name('website.companies.page');
Route::get('/companies/{slug}/blogs/{blogSlug}', [CompanyDirectoryController::class, 'blog'])->name('website.companies.blog');
Route::post('/companies/{slug}/reviews', [CompanyDirectoryController::class, 'storeReview'])->name('website.companies.reviews.store');
Route::post('/companies/{slug}/enquiries', [CompanyDirectoryController::class, 'storeEnquiry'])->name('website.companies.enquiries.store');
Route::post('/companies/{slug}/newsletter', [CompanyDirectoryController::class, 'storeNewsletter'])->name('website.companies.newsletter.store');
Route::get('/comparison/{slug}', [WebsiteController::class, 'comparison'])->name('website.comparison');
Route::get('/help-center', [WebsiteController::class, 'helpCenter'])->name('website.help-center');
Route::get('/whats-new', [WebsiteController::class, 'whatsNew'])->name('website.whats-new');
Route::get('/blogs', [WebsiteController::class, 'blogs'])->name('website.blogs');
Route::get('/blogs/{slug}', [WebsiteController::class, 'blogShow'])->name('website.blog.show');
Route::get('/pricing', [WebsiteController::class, 'pricing'])->name('website.pricing');
Route::get('/about', fn () => redirect('/about-us', 301));
Route::get('/contact', fn () => redirect('/help-center', 301));
Route::get('/{slug}', [WebsiteController::class, 'page'])
    ->where('slug', 'about-us|product-demo|drm-security|corporate-lms|ai|careers|privacy-policy|terms-and-conditions|support-migration|guides')
    ->name('website.page');

Route::get('/home-lms', [PublicController::class, 'home'])->name('home.lms');
Route::get('/courses', [PublicController::class, 'courses'])->name('public.courses');
Route::get('/courses/{course:slug}', [PublicController::class, 'courseShow'])->name('public.course');
Route::post('/courses/{course:slug}/reviews', [PublicController::class, 'storeCourseReview'])->name('public.course.reviews.store');
Route::post('/courses/{course:slug}/enquiries', [PublicController::class, 'storeCourseEnquiry'])->name('public.course.enquiries.store');
Route::middleware(['auth', 'role:learner,alumni'])->group(function () {
    Route::post('/courses/{course:slug}/checkout', [\App\Http\Controllers\CourseCheckoutController::class, 'start'])->name('courses.checkout.start');
    Route::post('/courses/checkout/complete', [\App\Http\Controllers\CourseCheckoutController::class, 'complete'])->name('courses.checkout.complete');
});
Route::post('/leads', [PublicController::class, 'captureLead'])->name('leads.capture');
Route::get('/lp/{slug}', [\App\Http\Controllers\PublicMarketingController::class, 'landingPage'])->name('public.landing.show');
Route::get('/lp/{slug}/cta', [\App\Http\Controllers\PublicMarketingController::class, 'landingCta'])->name('public.landing.cta');
Route::post('/lp/{slug}/lead', [\App\Http\Controllers\PublicMarketingController::class, 'landingLead'])->name('public.landing.lead');
Route::get('/webinars/{webinar:slug}/register', [\App\Http\Controllers\PublicMarketingController::class, 'webinarRegisterForm'])->name('public.webinars.register.form');
Route::post('/webinars/{webinar:slug}/register', [\App\Http\Controllers\PublicMarketingController::class, 'webinarRegister'])->name('public.webinars.register');
Route::get('/verify-certificate', [PublicCertificateController::class, 'verify'])->name('certificates.verify');

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
    Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('auth.social.redirect');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('auth.social.callback');
});

Route::get('/auth/2fa', [TwoFactorController::class, 'showChallenge'])->name('auth.2fa.challenge');
Route::post('/auth/2fa', [TwoFactorController::class, 'verifyChallenge'])->name('auth.2fa.verify');
Route::post('/auth/2fa/email', [TwoFactorController::class, 'sendEmailOtp'])->name('auth.2fa.email');

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

Route::prefix('company')->name('admin.')->middleware(['auth', 'role:admin,sub-admin,counselor', 'ip.access', 'device.active'])->group(function () {
    Route::post('exit-platform-view', [PlatformCompanyController::class, 'exitPanel'])->name('exit-platform-view');
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::post('courses/ai-analyze', [AdminCourseController::class, 'aiAnalyze'])->name('courses.ai-analyze');
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

    Route::post('live-classes/ai-analyze', [LiveClassController::class, 'aiAnalyze'])->name('live-classes.ai-analyze');
    Route::resource('live-classes', LiveClassController::class)->except(['show']);
    Route::get('live-classes/{liveClass}/attendance', [GamificationController::class, 'attendances'])->name('live-classes.attendance');
    Route::post('live-classes/{liveClass}/attendance', [GamificationController::class, 'markAttendance'])->name('gamification.attendances.mark');

    Route::get('gamification/rules', [GamificationController::class, 'rules'])->name('gamification.rules');
    Route::post('gamification/rules', [GamificationController::class, 'updateRules'])->name('gamification.rules.update');
    Route::get('gamification/badges', [GamificationController::class, 'badges'])->name('gamification.badges');
    Route::post('gamification/badges', [GamificationController::class, 'storeBadge'])->name('gamification.badges.store');
    Route::delete('gamification/badges/{badge}', [GamificationController::class, 'destroyBadge'])->name('gamification.badges.destroy');
    Route::get('gamification/challenges', [GamificationController::class, 'challenges'])->name('gamification.challenges');
    Route::post('gamification/challenges', [GamificationController::class, 'storeChallenge'])->name('gamification.challenges.store');
    Route::delete('gamification/challenges/{challenge}', [GamificationController::class, 'destroyChallenge'])->name('gamification.challenges.destroy');
    Route::get('gamification/leaderboard', [GamificationController::class, 'leaderboard'])->name('gamification.leaderboard');

    Route::get('crm/pipeline', [CrmController::class, 'pipeline'])->name('crm.pipeline');
    Route::get('crm/leads', [CrmController::class, 'leads'])->name('crm.leads');
    Route::get('crm/leads/{lead}', [CrmController::class, 'show'])->name('crm.leads.show');
    Route::post('crm/leads/{lead}/stage', [CrmController::class, 'updateStage'])->name('crm.leads.stage');
    Route::post('crm/leads/{lead}/assign', [CrmController::class, 'assign'])->name('crm.leads.assign');
    Route::post('crm/leads/{lead}/convert', [CrmController::class, 'convert'])->name('crm.leads.convert');
    Route::post('crm/leads/{lead}/notes', [CrmController::class, 'storeNote'])->name('crm.leads.notes');
    Route::post('crm/leads/{lead}/follow-ups', [CrmController::class, 'storeFollowUp'])->name('crm.leads.follow-ups');
    Route::post('crm/leads/{lead}/calls', [CrmController::class, 'storeCallLog'])->name('crm.leads.calls');
    Route::post('crm/leads/{lead}/messages', [CrmController::class, 'storeMessage'])->name('crm.leads.messages');
    Route::get('crm/follow-ups', [CrmController::class, 'followUps'])->name('crm.follow-ups');
    Route::post('crm/follow-ups/{followUp}/complete', [CrmController::class, 'completeFollowUp'])->name('crm.follow-ups.complete');
    Route::get('crm/call-logs', [CrmController::class, 'callLogs'])->name('crm.call-logs');
    Route::get('crm/counselor', [CrmController::class, 'counselorDashboard'])->name('crm.counselor');
    Route::get('crm/analytics', [CrmController::class, 'analytics'])->name('crm.analytics');

    Route::get('automations', [AutomationController::class, 'index'])->name('automations.index');
    Route::get('automations/create', [AutomationController::class, 'create'])->name('automations.create');
    Route::post('automations', [AutomationController::class, 'store'])->name('automations.store');
    Route::delete('automations/{automation}', [AutomationController::class, 'destroy'])->name('automations.destroy');
    Route::get('automations/{automation}/runs', [AutomationController::class, 'runs'])->name('automations.runs');
    Route::post('automations/{automation}/test', [AutomationController::class, 'test'])->name('automations.test');

    Route::get('landing-pages', [LandingPageController::class, 'index'])->name('landing-pages.index');
    Route::post('landing-pages', [LandingPageController::class, 'store'])->name('landing-pages.store');
    Route::delete('landing-pages/{landingPage}', [LandingPageController::class, 'destroy'])->name('landing-pages.destroy');
    Route::post('landing-pages/{landingPage}/toggle', [LandingPageController::class, 'toggle'])->name('landing-pages.toggle');

    Route::get('webinar-registrations', [WebinarRegistrationController::class, 'index'])->name('webinar-registrations.index');

    Route::get('ai', [AiCenterController::class, 'index'])->name('ai.index');
    Route::post('ai/settings', [AiCenterController::class, 'settings'])->name('ai.settings');
    Route::post('ai/generate', [AiCenterController::class, 'generate'])->name('ai.generate');
    Route::get('ai/{ai}', [AiCenterController::class, 'show'])->name('ai.show');
    Route::post('ai/{ai}/status', [AiCenterController::class, 'updateStatus'])->name('ai.status');

    Route::get('integrations', [IntegrationController::class, 'index'])->name('integrations.index');
    Route::post('integrations/telegram/test-send', [IntegrationController::class, 'testTelegram'])->name('integrations.telegram.test');
    Route::get('integrations/{provider}', [IntegrationController::class, 'edit'])->name('integrations.edit');
    Route::post('integrations/{provider}', [IntegrationController::class, 'update'])->name('integrations.update');
    Route::post('integrations/{provider}/test', [IntegrationController::class, 'test'])->name('integrations.test');

    Route::get('security', [SecurityController::class, 'index'])->name('security.index');
    Route::get('security/2fa/setup', [SecurityController::class, 'enable2faSetup'])->name('security.2fa.setup');
    Route::post('security/2fa/confirm', [SecurityController::class, 'confirm2fa'])->name('security.2fa.confirm');
    Route::post('security/2fa/disable', [SecurityController::class, 'disable2fa'])->name('security.2fa.disable');
    Route::delete('security/devices/{device}', [SecurityController::class, 'revokeDevice'])->name('security.devices.revoke');
    Route::post('security/ip-rules', [SecurityController::class, 'storeIpRule'])->name('security.ip-rules.store');
    Route::delete('security/ip-rules/{ipRule}', [SecurityController::class, 'destroyIpRule'])->name('security.ip-rules.destroy');
    Route::post('security/settings', [SecurityController::class, 'updateSettings'])->name('security.settings');
    Route::get('security/login-history', [SecurityController::class, 'loginHistory'])->name('security.login-history');

    Route::get('whitelabel', [WhiteLabelController::class, 'edit'])->name('whitelabel.edit');
    Route::post('whitelabel', [WhiteLabelController::class, 'update'])->name('whitelabel.update');
    Route::post('whitelabel/verify', [WhiteLabelController::class, 'markVerified'])->name('whitelabel.verify');

    Route::get('finance', [FinanceController::class, 'dashboard'])->name('finance.dashboard');
    Route::post('finance/sync', [FinanceController::class, 'syncFromOrders'])->name('finance.sync');
    Route::get('finance/ledger', [FinanceController::class, 'ledger'])->name('finance.ledger');
    Route::post('finance/ledger', [FinanceController::class, 'storeEntry'])->name('finance.ledger.store');
    Route::delete('finance/ledger/{entry}', [FinanceController::class, 'destroyEntry'])->name('finance.ledger.destroy');
    Route::get('finance/accounts', [FinanceController::class, 'accounts'])->name('finance.accounts');
    Route::post('finance/accounts', [FinanceController::class, 'storeAccount'])->name('finance.accounts.store');
    Route::get('finance/cash-book', [FinanceController::class, 'cashBook'])->name('finance.cash-book');
    Route::get('finance/bank-book', [FinanceController::class, 'bankBook'])->name('finance.bank-book');
    Route::get('finance/receipts', [FinanceController::class, 'receipts'])->name('finance.receipts');
    Route::get('finance/receipts/{receipt}', [FinanceController::class, 'showReceipt'])->name('finance.receipts.show');
    Route::get('finance/profit-loss', [FinanceController::class, 'profitLoss'])->name('finance.profit-loss');
    Route::get('finance/balance-sheet', [FinanceController::class, 'balanceSheet'])->name('finance.balance-sheet');
    Route::get('finance/tax-export', [FinanceController::class, 'taxExport'])->name('finance.tax-export');
    Route::get('finance/invoices', [FinanceController::class, 'invoices'])->name('finance.invoices');

    Route::get('hr/employees', [HrController::class, 'employees'])->name('hr.employees');
    Route::post('hr/employees', [HrController::class, 'storeEmployee'])->name('hr.employees.store');
    Route::get('hr/employees/{employee}', [HrController::class, 'showEmployee'])->name('hr.employees.show');
    Route::post('hr/employees/{employee}/documents', [HrController::class, 'storeDocument'])->name('hr.documents.store');
    Route::get('hr/attendance', [HrController::class, 'attendance'])->name('hr.attendance');
    Route::post('hr/attendance', [HrController::class, 'storeAttendance'])->name('hr.attendance.store');
    Route::post('hr/attendance/punch-in', [HrController::class, 'punchIn'])->name('hr.attendance.punch-in');
    Route::post('hr/attendance/punch-out', [HrController::class, 'punchOut'])->name('hr.attendance.punch-out');
    Route::get('hr/leaves', [HrController::class, 'leaves'])->name('hr.leaves');
    Route::post('hr/leaves', [HrController::class, 'storeLeave'])->name('hr.leaves.store');
    Route::post('hr/leaves/{leave}/review', [HrController::class, 'reviewLeave'])->name('hr.leaves.review');
    Route::get('hr/payroll', [HrController::class, 'payroll'])->name('hr.payroll');
    Route::post('hr/payroll', [HrController::class, 'storePayroll'])->name('hr.payroll.store');
    Route::get('hr/payroll/{payroll}', [HrController::class, 'showPayroll'])->name('hr.payroll.show');
    Route::get('hr/slips/{slip}', [HrController::class, 'salarySlip'])->name('hr.slips.show');

    Route::get('branches', [BranchController::class, 'index'])->name('branches.index');
    Route::post('branches', [BranchController::class, 'store'])->name('branches.store');
    Route::get('branches/reports', [BranchController::class, 'reports'])->name('branches.reports');
    Route::get('branches/{branch}', [BranchController::class, 'show'])->name('branches.show');
    Route::post('branches/{branch}/admins', [BranchController::class, 'assignAdmin'])->name('branches.admins');
    Route::post('branches/{branch}/users', [BranchController::class, 'assignUser'])->name('branches.users');
    Route::post('branches/{branch}/share', [BranchController::class, 'updateShare'])->name('branches.share');

    Route::get('placements/companies', [PlacementController::class, 'companies'])->name('placements.companies');
    Route::post('placements/companies', [PlacementController::class, 'storeCompany'])->name('placements.companies.store');
    Route::get('placements/jobs', [PlacementController::class, 'jobs'])->name('placements.jobs');
    Route::post('placements/jobs', [PlacementController::class, 'storeJob'])->name('placements.jobs.store');
    Route::get('placements/applications', [PlacementController::class, 'applications'])->name('placements.applications');
    Route::post('placements/applications/{application}', [PlacementController::class, 'updateApplication'])->name('placements.applications.update');
    Route::get('placements/reports', [PlacementController::class, 'reports'])->name('placements.reports');

    Route::get('library', [DigitalLibraryController::class, 'index'])->name('library.index');
    Route::post('library', [DigitalLibraryController::class, 'store'])->name('library.store');
    Route::post('library/import', [DigitalLibraryController::class, 'importExisting'])->name('library.import');
    Route::delete('library/{libraryItem}', [DigitalLibraryController::class, 'destroy'])->name('library.destroy');

    Route::post('quizzes/ai-analyze', [QuizController::class, 'aiAnalyze'])->name('quizzes.ai-analyze');
    Route::get('quizzes/{lesson}/edit', [QuizController::class, 'edit'])->name('quizzes.edit');
    Route::put('quizzes/{lesson}', [QuizController::class, 'update'])->name('quizzes.update');
    Route::delete('quizzes/{lesson}', [QuizController::class, 'destroy'])->name('quizzes.destroy');
    Route::resource('quizzes', QuizController::class)->only(['index', 'create', 'store']);
    Route::post('assignments/ai-analyze', [AssignmentController::class, 'aiAnalyze'])->name('assignments.ai-analyze');
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
    Route::post('orders/{order}/gst-invoice', [GstInvoiceController::class, 'generate'])->name('orders.gst-invoice');

    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');

    Route::resource('checkout-consents', CheckoutConsentController::class)->except(['show']);
    Route::get('checkout-consents-report', [CheckoutConsentController::class, 'report'])->name('checkout-consents.report');

    Route::get('wallets/transactions', [WalletController::class, 'transactions'])->name('wallets.transactions');
    Route::get('wallets/settings', [WalletController::class, 'settings'])->name('wallets.settings');
    Route::put('wallets/settings', [WalletController::class, 'updateSettings'])->name('wallets.settings.update');
    Route::resource('wallets', WalletController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('wallets/{wallet}/top-up', [WalletController::class, 'topUp'])->name('wallets.top-up');
    Route::post('wallets/{wallet}/adjust', [WalletController::class, 'adjust'])->name('wallets.adjust');
    Route::post('wallets/{wallet}/freeze', [WalletController::class, 'freeze'])->name('wallets.freeze');

    Route::get('referrals', [ReferralController::class, 'index'])->name('referrals.index');
    Route::post('referrals/codes', [ReferralController::class, 'storeCode'])->name('referrals.codes.store');
    Route::post('referrals/codes/{referralCode}/toggle', [ReferralController::class, 'toggleCode'])->name('referrals.codes.toggle');
    Route::post('referrals/apply', [ReferralController::class, 'apply'])->name('referrals.apply');
    Route::post('referrals/{referral}/reward', [ReferralController::class, 'reward'])->name('referrals.reward');
    Route::get('referrals/settings', [ReferralController::class, 'settings'])->name('referrals.settings');
    Route::put('referrals/settings', [ReferralController::class, 'updateSettings'])->name('referrals.settings.update');

    Route::get('subscriptions/plans', [SubscriptionController::class, 'plans'])->name('subscriptions.plans');
    Route::get('subscriptions/plans/create', [SubscriptionController::class, 'create'])->name('subscriptions.plans.create');
    Route::post('subscriptions/plans', [SubscriptionController::class, 'store'])->name('subscriptions.plans.store');
    Route::get('subscriptions/plans/{plan}/edit', [SubscriptionController::class, 'edit'])->name('subscriptions.plans.edit');
    Route::put('subscriptions/plans/{plan}', [SubscriptionController::class, 'update'])->name('subscriptions.plans.update');
    Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::post('subscriptions', [SubscriptionController::class, 'storeSubscription'])->name('subscriptions.store');
    Route::get('subscriptions/{subscription}', [SubscriptionController::class, 'show'])->name('subscriptions.show');
    Route::post('subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::post('subscriptions/{subscription}/pause', [SubscriptionController::class, 'pause'])->name('subscriptions.pause');
    Route::post('subscriptions/{subscription}/resume', [SubscriptionController::class, 'resume'])->name('subscriptions.resume');

    Route::get('gst-invoices', [GstInvoiceController::class, 'index'])->name('gst-invoices.index');
    Route::get('gst-invoices/{gstInvoice}', [GstInvoiceController::class, 'show'])->name('gst-invoices.show');
    Route::get('gst-invoices/{gstInvoice}/download', [GstInvoiceController::class, 'download'])->name('gst-invoices.download');
    Route::post('gst-invoices/{gstInvoice}/credit-note', [GstInvoiceController::class, 'creditNote'])->name('gst-invoices.credit-note');

    Route::get('affiliates/settings', [AffiliateController::class, 'settings'])->name('affiliates.settings');
    Route::put('affiliates/settings', [AffiliateController::class, 'updateSettings'])->name('affiliates.settings.update');
    Route::resource('affiliates', AffiliateController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('affiliates/{affiliate}/approve', [AffiliateController::class, 'approve'])->name('affiliates.approve');
    Route::post('affiliates/{affiliate}/reject', [AffiliateController::class, 'reject'])->name('affiliates.reject');
    Route::post('affiliates/{affiliate}/suspend', [AffiliateController::class, 'suspend'])->name('affiliates.suspend');
    Route::post('affiliates/{affiliate}/links', [AffiliateController::class, 'storeLink'])->name('affiliates.links.store');
    Route::post('affiliates/{affiliate}/payouts', [AffiliateController::class, 'storePayout'])->name('affiliates.payouts.store');
    Route::post('affiliates/payouts/{payout}/paid', [AffiliateController::class, 'markPayoutPaid'])->name('affiliates.payouts.paid');

    Route::resource('batches', AdminBatchController::class);
    Route::post('batches/{batch}/learners', [AdminBatchController::class, 'addLearner'])->name('batches.learners.add');
    Route::delete('batches/{batch}/learners/{user}', [AdminBatchController::class, 'removeLearner'])->name('batches.learners.remove');

    Route::post('instructors/ai-analyze', [InstructorController::class, 'aiAnalyze'])->name('instructors.ai-analyze');
    Route::resource('instructors', InstructorController::class);
    Route::post('instructors/{instructor}/courses', [InstructorController::class, 'assignCourse'])->name('instructors.courses.assign');
    Route::delete('instructors/{instructor}/courses/{course}', [InstructorController::class, 'removeCourse'])->name('instructors.courses.remove');
    Route::post('instructors/{instructor}/batches', [InstructorController::class, 'assignBatch'])->name('instructors.batches.assign');

    Route::get('sub-admins', [SubAdminController::class, 'index'])->name('sub-admins.index');
    Route::get('sub-admins/wizard', [SubAdminWizardController::class, 'create'])->name('sub-admins.wizard');
    Route::post('sub-admins/wizard/ai-analyze', [SubAdminWizardController::class, 'aiAnalyze'])->name('sub-admins.wizard.ai-analyze');
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
    Route::post('communities/{community}/announcements', [AdminCommunityController::class, 'storeAnnouncement'])->name('communities.announcements.store');
    Route::post('community-announcements/{announcement}/push', [AdminCommunityController::class, 'pushAnnouncement'])->name('communities.announcements.push');

    Route::get('discussions', [DiscussionController::class, 'index'])->name('discussions.index');
    Route::get('discussions/{discussion}', [DiscussionController::class, 'show'])->name('discussions.show');
    Route::post('discussions/{discussion}/lock', [DiscussionController::class, 'lock'])->name('discussions.lock');
    Route::delete('discussions/{discussion}', [DiscussionController::class, 'destroy'])->name('discussions.destroy');

    Route::get('certificates', [CertificateController::class, 'index'])->name('certificates.index');
    Route::get('certificates/templates', [CertificateController::class, 'templates'])->name('certificates.templates');
    Route::post('certificates/templates', [CertificateController::class, 'storeTemplate'])->name('certificates.templates.store');
    Route::post('certificates/issue', [CertificateController::class, 'issue'])->name('certificates.issue');
    Route::get('certificates/renewals', [CertificateRenewalController::class, 'index'])->middleware('permission:certificates_renewal,view')->name('certificates.renewals.index');
    Route::put('certificates/templates/{template}/renewal', [CertificateRenewalController::class, 'updateTemplate'])->middleware('permission:certificates_renewal,edit')->name('certificates.templates.renewal');
    Route::post('certificates/renewals/bulk', [CertificateRenewalController::class, 'bulkRenew'])->middleware('permission:certificates_renewal,manage')->name('certificates.renewals.bulk');

    Route::get('alumni', [AlumniController::class, 'index'])->middleware('permission:alumni,view')->name('alumni.index');
    Route::get('proctoring', [ProctoringController::class, 'index'])->middleware('permission:proctoring,view')->name('proctoring.index');
    Route::put('proctoring', [ProctoringController::class, 'update'])->middleware('permission:proctoring,edit')->name('proctoring.update');
    Route::get('compliance', [ComplianceController::class, 'index'])->middleware('permission:compliance,view')->name('compliance.index');
    Route::put('compliance', [ComplianceController::class, 'update'])->middleware('permission:compliance,edit')->name('compliance.update');
    Route::get('notification-center', [NotificationCenterController::class, 'index'])->middleware('permission:notifications,view')->name('notifications.index');
    Route::put('notification-center', [NotificationCenterController::class, 'update'])->middleware('permission:notifications,edit')->name('notifications.update');
    Route::get('parent-links', [ParentLinkController::class, 'index'])->middleware('permission:parent,view')->name('parent-links.index');
    Route::post('parent-links', [ParentLinkController::class, 'store'])->middleware('permission:parent,view')->name('parent-links.store');
    Route::delete('parent-links/{link}', [ParentLinkController::class, 'destroy'])->middleware('permission:parent,view')->name('parent-links.destroy');

    Route::prefix('website-builder')->name('website-builder.')->middleware('permission:settings,view')->group(function () {
        Route::get('/', [WebsiteBuilderController::class, 'index'])->name('index');
        Route::get('pages/create', [WebsiteBuilderController::class, 'createPage'])->name('pages.create');
        Route::post('pages', [WebsiteBuilderController::class, 'storePage'])->name('pages.store');
        Route::get('pages/{page}/edit', [WebsiteBuilderController::class, 'editPage'])->name('pages.edit');
        Route::put('pages/{page}', [WebsiteBuilderController::class, 'updatePage'])->name('pages.update');
        Route::delete('pages/{page}', [WebsiteBuilderController::class, 'destroyPage'])->name('pages.destroy');
        Route::post('pages/{page}/blocks', [WebsiteBuilderController::class, 'storeBlock'])->name('blocks.store');
        Route::put('blocks/{block}', [WebsiteBuilderController::class, 'updateBlock'])->name('blocks.update');
        Route::delete('blocks/{block}', [WebsiteBuilderController::class, 'destroyBlock'])->name('blocks.destroy');
        Route::post('pages/{page}/blocks/reorder', [WebsiteBuilderController::class, 'reorderBlocks'])->name('blocks.reorder');
        Route::get('menus', [WebsiteBuilderController::class, 'menus'])->name('menus');
        Route::post('menus', [WebsiteBuilderController::class, 'storeMenu'])->name('menus.store');
        Route::put('menus/{menu}', [WebsiteBuilderController::class, 'updateMenu'])->name('menus.update');
        Route::delete('menus/{menu}', [WebsiteBuilderController::class, 'destroyMenu'])->name('menus.destroy');
        Route::post('menus/reorder', [WebsiteBuilderController::class, 'reorderMenus'])->name('menus.reorder');
        Route::get('seo', [WebsiteBuilderController::class, 'seo'])->name('seo');
        Route::put('seo', [WebsiteBuilderController::class, 'updateSeo'])->name('seo.update');
    });

    Route::get('marketing/coupons', [MarketingController::class, 'coupons'])->name('marketing.coupons');
    Route::post('marketing/coupons', [MarketingController::class, 'storeCoupon'])->name('marketing.coupons.store');
    Route::delete('marketing/coupons/{coupon}', [MarketingController::class, 'destroyCoupon'])->name('marketing.coupons.destroy');
    Route::get('marketing/campaigns', [MarketingController::class, 'campaigns'])->name('marketing.campaigns');
    Route::post('marketing/campaigns', [MarketingController::class, 'storeCampaign'])->name('marketing.campaigns.store');
    Route::post('marketing/campaigns/{campaign}/send', [MarketingController::class, 'sendCampaign'])->name('marketing.campaigns.send');
    Route::get('marketing/campaigns/{campaign}/sends', [MarketingController::class, 'campaignSends'])->name('marketing.campaigns.sends');
    Route::get('marketing/leads', [MarketingController::class, 'leads'])->name('marketing.leads');
    Route::post('marketing/leads', [MarketingController::class, 'storeLead'])->name('marketing.leads.store');
    Route::put('marketing/leads/{lead}', [MarketingController::class, 'updateLead'])->name('marketing.leads.update');
    Route::post('marketing/leads/{lead}/assign', [MarketingController::class, 'assignLead'])->name('marketing.leads.assign');
    Route::post('marketing/leads/{lead}/convert', [MarketingController::class, 'convertLead'])->name('marketing.leads.convert');

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
    Route::get('profile/preview', [CompanyProfileController::class, 'preview'])->name('company-profile.preview');
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
    Route::get('companies/create', [PlatformCompanyController::class, 'create'])->name('companies.create');
    Route::post('companies', [PlatformCompanyController::class, 'store'])->name('companies.store');
    Route::get('companies/{company}', [PlatformCompanyController::class, 'show'])->name('companies.show');
    Route::get('companies/{company}/edit', [PlatformCompanyController::class, 'edit'])->name('companies.edit');
    Route::put('companies/{company}', [PlatformCompanyController::class, 'update'])->name('companies.update');
    Route::post('companies/{company}/toggle-active', [PlatformCompanyController::class, 'toggleActive'])->name('companies.toggle-active');
    Route::post('companies/{company}/toggle-public', [PlatformCompanyController::class, 'togglePublic'])->name('companies.toggle-public');
    Route::post('companies/{company}/assign-package', [PlatformCompanyController::class, 'assignPackage'])->name('companies.assign-package');
    Route::post('companies/{company}/enter-panel', [PlatformCompanyController::class, 'enterPanel'])->name('companies.enter-panel');
    Route::get('branding', [PlatformBrandingController::class, 'index'])->name('branding.index');
    Route::get('branding/{company}', [PlatformBrandingController::class, 'show'])->name('branding.show');
    Route::post('branding/{company}/verify', [PlatformBrandingController::class, 'verify'])->name('branding.verify');
    Route::get('users', [PlatformUserController::class, 'index'])->name('users.index');
    Route::get('users/create', [PlatformUserController::class, 'create'])->name('users.create');
    Route::post('users', [PlatformUserController::class, 'store'])->name('users.store');
    Route::get('users/{user}', [PlatformUserController::class, 'show'])->name('users.show');
    Route::get('users/{user}/edit', [PlatformUserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [PlatformUserController::class, 'update'])->name('users.update');
    Route::post('users/{user}/toggle-active', [PlatformUserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::post('users/{user}/role', [PlatformUserController::class, 'updateRole'])->name('users.role');
    Route::post('users/{user}/reset-password', [PlatformUserController::class, 'resetPassword'])->name('users.reset-password');
    Route::post('users/{user}/force-logout', [PlatformUserController::class, 'forceLogout'])->name('users.force-logout');
    Route::post('users/{user}/devices/{device}/revoke', [PlatformUserController::class, 'revokeDevice'])->name('users.devices.revoke');
    Route::get('activity-logs', [PlatformActivityController::class, 'index'])->name('activity.index');
    Route::get('activity-logs/export', [PlatformActivityController::class, 'export'])->name('activity.export');
    Route::get('activity-logs/login-audit', [PlatformActivityController::class, 'loginAudit'])->name('activity.login-audit');
    Route::get('activity-logs/live-sessions', [PlatformActivityController::class, 'liveSessions'])->name('activity.live-sessions');
    Route::post('activity-logs/sessions/{session}/revoke', [PlatformActivityController::class, 'revokeSession'])->name('activity.sessions.revoke');
    Route::get('activity-logs/{activityLog}', [PlatformActivityController::class, 'show'])->name('activity.show');

    Route::get('sales/orders', [PlatformSalesController::class, 'orders'])->name('sales.orders');
    Route::get('sales/orders/export', [PlatformSalesController::class, 'ordersExport'])->name('sales.orders.export');
    Route::get('sales/orders/{order}', [PlatformSalesController::class, 'orderShow'])->name('sales.orders.show');
    Route::get('sales/payments', [PlatformSalesController::class, 'payments'])->name('sales.payments');
    Route::get('sales/payments/export', [PlatformSalesController::class, 'paymentsExport'])->name('sales.payments.export');
    Route::get('sales/payments/{payment}', [PlatformSalesController::class, 'paymentShow'])->name('sales.payments.show');
    Route::get('sales/revenue', [PlatformSalesController::class, 'revenueByInstitute'])->name('sales.revenue');
    Route::get('sales/revenue/export', [PlatformSalesController::class, 'revenueExport'])->name('sales.revenue.export');
    Route::get('sales/packages', [PlatformSalesController::class, 'packagesOverview'])->name('sales.packages');
    Route::get('sales/packages/export', [PlatformSalesController::class, 'packagesExport'])->name('sales.packages.export');

    Route::get('academic/courses', [PlatformAcademicController::class, 'courses'])->name('academic.courses');
    Route::get('academic/courses/export', [PlatformAcademicController::class, 'coursesExport'])->name('academic.courses.export');
    Route::get('academic/courses/{course}', [PlatformAcademicController::class, 'courseShow'])->name('academic.courses.show');
    Route::get('academic/enrollments', [PlatformAcademicController::class, 'enrollments'])->name('academic.enrollments');
    Route::get('academic/enrollments/export', [PlatformAcademicController::class, 'enrollmentsExport'])->name('academic.enrollments.export');
    Route::get('academic/enrollments/{enrollment}', [PlatformAcademicController::class, 'enrollmentShow'])->name('academic.enrollments.show');
    Route::get('academic/live-classes', [PlatformAcademicController::class, 'liveClasses'])->name('academic.live-classes');
    Route::get('academic/live-classes/export', [PlatformAcademicController::class, 'liveClassesExport'])->name('academic.live-classes.export');
    Route::get('academic/live-classes/{event}', [PlatformAcademicController::class, 'liveClassShow'])->name('academic.live-classes.show');
    Route::get('academic/certificates', [PlatformAcademicController::class, 'certificates'])->name('academic.certificates');
    Route::get('academic/certificates/export', [PlatformAcademicController::class, 'certificatesExport'])->name('academic.certificates.export');
    Route::get('academic/certificates/{certificate}', [PlatformAcademicController::class, 'certificateShow'])->name('academic.certificates.show');

    Route::get('reports/performance', [PlatformReportController::class, 'performance'])->name('reports.performance');
    Route::get('reports/performance/export', [PlatformReportController::class, 'performanceExport'])->name('reports.performance.export');
    Route::get('reports/growth', [PlatformReportController::class, 'growth'])->name('reports.growth');
    Route::get('reports/growth/export', [PlatformReportController::class, 'growthExport'])->name('reports.growth.export');
    Route::get('reports/signup-funnel', [PlatformReportController::class, 'signupFunnel'])->name('reports.signup-funnel');
    Route::get('reports/signup-funnel/export', [PlatformReportController::class, 'signupFunnelExport'])->name('reports.signup-funnel.export');

    Route::get('integrations', [PlatformIntegrationsController::class, 'index'])->name('integrations.index');
    Route::get('roles', [PlatformRoleController::class, 'index'])->name('roles.index');
    Route::post('roles', [PlatformRoleController::class, 'store'])->name('roles.store');
    Route::post('roles/seed-permissions', [PlatformRoleController::class, 'seedPermissions'])->name('roles.seed-permissions');
    Route::get('roles/{role}/edit', [PlatformRoleController::class, 'edit'])->name('roles.edit');
    Route::put('roles/{role}', [PlatformRoleController::class, 'update'])->name('roles.update');
    Route::delete('roles/{role}', [PlatformRoleController::class, 'destroy'])->name('roles.destroy');
    Route::get('security', [PlatformSecurityController::class, 'index'])->name('security.index');
    Route::put('security', [PlatformSecurityController::class, 'update'])->name('security.update');
    Route::get('health', [PlatformHealthController::class, 'index'])->name('health.index');
    Route::post('health/clear-cache', [PlatformHealthController::class, 'clearCache'])->name('health.clear-cache');
    Route::post('health/clear-config', [PlatformHealthController::class, 'clearConfig'])->name('health.clear-config');
    Route::post('health/clear-views', [PlatformHealthController::class, 'clearViews'])->name('health.clear-views');
    Route::post('health/clear-routes', [PlatformHealthController::class, 'clearRoutes'])->name('health.clear-routes');

    Route::get('announcements', [PlatformAnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('announcements/create', [PlatformAnnouncementController::class, 'create'])->name('announcements.create');
    Route::post('announcements', [PlatformAnnouncementController::class, 'store'])->name('announcements.store');
    Route::get('announcements/{announcement}/edit', [PlatformAnnouncementController::class, 'edit'])->name('announcements.edit');
    Route::put('announcements/{announcement}', [PlatformAnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('announcements/{announcement}', [PlatformAnnouncementController::class, 'destroy'])->name('announcements.destroy');

    Route::get('tickets', [PlatformTicketController::class, 'index'])->name('tickets.index');
    Route::get('tickets/create', [PlatformTicketController::class, 'create'])->name('tickets.create');
    Route::post('tickets', [PlatformTicketController::class, 'store'])->name('tickets.store');
    Route::get('tickets/{ticket}', [PlatformTicketController::class, 'show'])->name('tickets.show');
    Route::post('tickets/{ticket}/reply', [PlatformTicketController::class, 'reply'])->name('tickets.reply');
    Route::post('tickets/{ticket}/close', [PlatformTicketController::class, 'close'])->name('tickets.close');
    Route::post('tickets/{ticket}/reopen', [PlatformTicketController::class, 'reopen'])->name('tickets.reopen');
    Route::put('tickets/{ticket}/meta', [PlatformTicketController::class, 'updateMeta'])->name('tickets.meta');

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

    Route::get('courses', [InstructorCourseController::class, 'index'])->name('courses.index');
    Route::get('courses/create', [InstructorCourseController::class, 'create'])->name('courses.create');
    Route::post('courses', [InstructorCourseController::class, 'store'])->name('courses.store');
    Route::get('courses/{course}', [InstructorCourseController::class, 'show'])->name('courses.show');
    Route::get('courses/{course}/edit', [InstructorCourseController::class, 'edit'])->name('courses.edit');
    Route::put('courses/{course}', [InstructorCourseController::class, 'update'])->name('courses.update');
    Route::post('courses/{course}/sections', [InstructorCourseController::class, 'storeSection'])->name('courses.sections.store');
    Route::post('courses/{course}/sections/{section}/lessons', [InstructorCourseController::class, 'storeLesson'])->name('courses.lessons.store');
    Route::put('courses/{course}/lessons/{lesson}', [InstructorCourseController::class, 'updateLesson'])->name('courses.lessons.update');

    Route::get('live-classes', [InstructorLiveClassController::class, 'index'])->name('live-classes.index');
    Route::get('live-classes/create', [InstructorLiveClassController::class, 'create'])->name('live-classes.create');
    Route::post('live-classes', [InstructorLiveClassController::class, 'store'])->name('live-classes.store');
    Route::get('live-classes/{event}', [InstructorLiveClassController::class, 'show'])->name('live-classes.show');
    Route::get('live-classes/{event}/edit', [InstructorLiveClassController::class, 'edit'])->name('live-classes.edit');
    Route::put('live-classes/{event}', [InstructorLiveClassController::class, 'update'])->name('live-classes.update');
    Route::post('live-classes/{event}/attendance', [InstructorLiveClassController::class, 'markAttendance'])->name('live-classes.attendance');

    Route::get('assessments', [InstructorAssessmentController::class, 'index'])->name('assessments.index');
    Route::get('assessments/create', [InstructorAssessmentController::class, 'create'])->name('assessments.create');
    Route::post('assessments', [InstructorAssessmentController::class, 'store'])->name('assessments.store');
    Route::get('assessments/{lesson}/submissions', [InstructorAssessmentController::class, 'submissions'])->name('assessments.submissions');
    Route::post('assessments/submissions/{submission}/grade', [InstructorAssessmentController::class, 'grade'])->name('assessments.grade');

    Route::get('students', [InstructorStudentController::class, 'index'])->name('students.index');
    Route::get('students/{user}', [InstructorStudentController::class, 'show'])->name('students.show');

    Route::get('discussions', [InstructorDiscussionController::class, 'index'])->name('discussions.index');
    Route::get('discussions/{discussion}', [InstructorDiscussionController::class, 'show'])->name('discussions.show');
    Route::post('discussions/{discussion}/reply', [InstructorDiscussionController::class, 'reply'])->name('discussions.reply');
    Route::post('discussions/{discussion}/resolve', [InstructorDiscussionController::class, 'resolve'])->name('discussions.resolve');

    Route::get('certificates', [InstructorCertificateController::class, 'index'])->name('certificates.index');
    Route::get('certificates/create', [InstructorCertificateController::class, 'create'])->name('certificates.create');
    Route::post('certificates', [InstructorCertificateController::class, 'store'])->name('certificates.store');

    Route::get('reports', [InstructorReportController::class, 'index'])->name('reports.index');

    Route::get('/ai', [InstructorAiController::class, 'index'])->name('ai.index');
    Route::post('/ai/generate', [InstructorAiController::class, 'generate'])->name('ai.generate');
});

Route::prefix('learner')->name('learner.')->middleware(['auth', 'role:learner,alumni'])->group(function () {
    Route::get('/', [LearnerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/courses', [LearnerCourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course:slug}', [LearnerCourseController::class, 'show'])->name('courses.show');
    Route::get('/lessons/{lesson}', [LearnerCourseController::class, 'lesson'])->name('lessons.show');
    Route::post('/lessons/{lesson}/complete', [LearnerCourseController::class, 'complete'])->name('lessons.complete');
    Route::post('/lessons/{lesson}/incomplete', [LearnerCourseController::class, 'incomplete'])->name('lessons.incomplete');
    Route::post('/courses/{course:slug}/certificate', [LearnerCourseController::class, 'issueCertificate'])->name('courses.certificate.issue');
    Route::get('/certificates/{certificate}/download', [LearnerCourseController::class, 'downloadCertificate'])->name('certificates.download');
    Route::get('/certificates', [LearnerDashboardController::class, 'certificates'])->name('certificates');
    Route::get('/certificates/{certificate}/renew', [LearnerCertificateRenewalController::class, 'show'])->name('certificates.renew');
    Route::post('/certificates/{certificate}/renew', [LearnerCertificateRenewalController::class, 'start'])->name('certificates.renew.start');
    Route::post('/certificates/renew/complete', [LearnerCertificateRenewalController::class, 'complete'])->name('certificates.renew.complete');
    Route::get('/communities', [LearnerCommunityController::class, 'index'])->name('communities.index');
    Route::get('/communities/{community:slug}', [LearnerCommunityController::class, 'show'])->name('communities.show');
    Route::get('/wallet', [LearnerWalletController::class, 'index'])->name('wallet.index');
    Route::get('/gamification', [LearnerGamificationController::class, 'profile'])->name('gamification.profile');
    Route::get('/gamification/leaderboard', [LearnerGamificationController::class, 'leaderboard'])->name('gamification.leaderboard');
    Route::get('/ai/chat', [LearnerAiAssistantController::class, 'chat'])->name('ai.chat');
    Route::post('/ai/chat', [LearnerAiAssistantController::class, 'sendChat'])->name('ai.chat.send');
    Route::get('/ai/planner', [LearnerAiAssistantController::class, 'planner'])->name('ai.planner');
    Route::post('/ai/planner', [LearnerAiAssistantController::class, 'createPlanner'])->name('ai.planner.create');
    Route::get('/placements', [LearnerPlacementController::class, 'index'])->name('placements.index');
    Route::get('/placements/applications', [LearnerPlacementController::class, 'myApplications'])->name('placements.applications');
    Route::get('/placements/resume', [LearnerPlacementController::class, 'resumeBuilder'])->name('placements.resume');
    Route::get('/placements/{job}', [LearnerPlacementController::class, 'show'])->name('placements.show');
    Route::post('/placements/{job}/apply', [LearnerPlacementController::class, 'apply'])->name('placements.apply');
    Route::get('/library', [LearnerLibraryController::class, 'index'])->name('library.index');
    Route::get('/library/{item}', [LearnerLibraryController::class, 'show'])->name('library.show');
    Route::get('/library/{item}/read', [LearnerLibraryController::class, 'read'])->name('library.read');
    Route::get('/library/{item}/download', [LearnerLibraryController::class, 'download'])->name('library.download');
    Route::get('/media/{token}', [MediaStreamController::class, 'stream'])->name('media.stream');
    Route::post('/media/heartbeat', [MediaStreamController::class, 'heartbeat'])->name('media.heartbeat');
    Route::get('/security', [LearnerSecurityController::class, 'index'])->name('security.index');
    Route::get('/security/2fa/setup', [LearnerSecurityController::class, 'setup2fa'])->name('security.2fa.setup');
    Route::post('/security/2fa/confirm', [LearnerSecurityController::class, 'confirm2fa'])->name('security.2fa.confirm');
    Route::post('/security/2fa/disable', [LearnerSecurityController::class, 'disable2fa'])->name('security.2fa.disable');
    Route::delete('/security/devices/{device}', [LearnerSecurityController::class, 'revokeDevice'])->name('security.devices.revoke');
});

Route::prefix('alumni')->name('alumni.')->middleware(['auth', 'role:alumni'])->group(function () {
    Route::get('/', [AlumniDashboardController::class, 'index'])->name('dashboard');
    Route::get('/certificates', [AlumniDashboardController::class, 'certificates'])->name('certificates');
    Route::get('/certificates/{certificate}/renew', [LearnerCertificateRenewalController::class, 'show'])->name('certificates.renew');
    Route::post('/certificates/{certificate}/renew', [LearnerCertificateRenewalController::class, 'start'])->name('certificates.renew.start');
    Route::post('/certificates/renew/complete', [LearnerCertificateRenewalController::class, 'complete'])->name('certificates.renew.complete');
});

Route::prefix('parent')->name('parent.')->middleware(['auth', 'role:parent'])->group(function () {
    Route::get('/', [ParentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/learners', [ParentDashboardController::class, 'learners'])->name('learners');
    Route::get('/learners/{learner}', [ParentDashboardController::class, 'learnerShow'])->name('learners.show');
    Route::get('/attendance', [ParentDashboardController::class, 'attendance'])->name('attendance');
    Route::get('/performance', [ParentDashboardController::class, 'performance'])->name('performance');
    Route::get('/progress', [ParentDashboardController::class, 'progress'])->name('progress');
    Route::get('/fees', [ParentDashboardController::class, 'fees'])->name('fees');
    Route::get('/notifications', [ParentDashboardController::class, 'notifications'])->name('notifications');
    Route::get('/certificates', [ParentDashboardController::class, 'certificates'])->name('certificates');
    Route::get('/certificates/{certificate}/download', [ParentDashboardController::class, 'downloadCertificate'])->name('certificates.download');
});
