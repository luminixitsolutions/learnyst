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
use App\Http\Controllers\Admin\UserModuleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Instructor\DashboardController as InstructorDashboardController;
use App\Http\Controllers\Learner\CommunityController as LearnerCommunityController;
use App\Http\Controllers\Learner\CourseController as LearnerCourseController;
use App\Http\Controllers\Learner\DashboardController as LearnerDashboardController;
use App\Http\Controllers\Platform\PlatformActivityController;
use App\Http\Controllers\Platform\PlatformCompanyController;
use App\Http\Controllers\Platform\PlatformDashboardController;
use App\Http\Controllers\Platform\PlatformSettingController;
use App\Http\Controllers\Platform\PlatformUserController;
use App\Http\Controllers\Admin\UtilitiesController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/courses', [PublicController::class, 'courses'])->name('public.courses');
Route::get('/courses/{course:slug}', [PublicController::class, 'courseShow'])->name('public.course');
Route::post('/leads', [PublicController::class, 'captureLead'])->name('leads.capture');
Route::get('/verify-certificate', [CertificateController::class, 'verify'])->name('certificates.verify');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/admin/login', [LoginController::class, 'showPlatformLoginForm'])->name('platform.login');
    Route::post('/admin/login', [LoginController::class, 'loginPlatform'])->name('platform.login.submit');
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
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

    Route::resource('mock-tests', MockTestController::class)->except(['show', 'edit', 'update']);
    Route::resource('test-series', TestSeriesController::class)->except(['show', 'edit', 'update']);
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
    Route::resource('quizzes', QuizController::class)->only(['index', 'create', 'store']);
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

    foreach ([
        'contacts' => 'contacts.index',
        'legal-documents' => 'legal-documents.index',
    ] as $uri => $routeName) {
        Route::get($uri, fn () => app(UserModuleController::class)->show($uri))->name($routeName);
    }

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
});

Route::prefix('admin')->name('platform.')->middleware(['auth', 'role:super-admin'])->group(function () {
    Route::get('/', [PlatformDashboardController::class, 'index'])->name('dashboard');
    Route::get('companies', [PlatformCompanyController::class, 'index'])->name('companies.index');
    Route::get('users', [PlatformUserController::class, 'index'])->name('users.index');
    Route::get('activity-logs', [PlatformActivityController::class, 'index'])->name('activity.index');
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
    Route::get('/communities', [LearnerCommunityController::class, 'index'])->name('communities.index');
    Route::get('/communities/{community:slug}', [LearnerCommunityController::class, 'show'])->name('communities.show');
});
