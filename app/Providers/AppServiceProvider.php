<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(\App\Contracts\SmsProviderInterface::class, \App\Services\Messaging\LogSmsProvider::class);
        $this->app->bind(\App\Contracts\WhatsAppProviderInterface::class, \App\Services\Messaging\LogWhatsAppProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Route::bind('learner', fn ($value) => \App\Models\User::findOrFail($value));
        \Illuminate\Support\Facades\Route::bind('instructor', fn ($value) => \App\Models\User::findOrFail($value));
        \Illuminate\Support\Facades\Route::bind('subAdmin', fn ($value) => \App\Models\User::findOrFail($value));
        \Illuminate\Support\Facades\Route::bind('enrollment', fn ($value) => \App\Models\CourseEnrollment::findOrFail($value));
        \Illuminate\Support\Facades\Route::bind('track', fn ($value) => \App\Models\InstructorTrack::findOrFail($value));
        \Illuminate\Support\Facades\Route::bind('ebook', fn ($value) => \App\Models\Ebook::findOrFail($value));
        \Illuminate\Support\Facades\Route::bind('podcast', fn ($value) => \App\Models\Podcast::findOrFail($value));
        \Illuminate\Support\Facades\Route::bind('webinar', fn ($value) => \App\Models\Webinar::findOrFail($value));
        \Illuminate\Support\Facades\Route::bind('custom_product', fn ($value) => \App\Models\CustomProduct::findOrFail($value));
        \Illuminate\Support\Facades\Route::bind('question_pool', fn ($value) => \App\Models\QuestionPool::findOrFail($value));
        \Illuminate\Support\Facades\Route::bind('question', fn ($value) => \App\Models\Question::findOrFail($value));
        \Illuminate\Support\Facades\Route::bind('liveClass', fn ($value) => \App\Models\ScheduledEvent::findOrFail($value));
        \Illuminate\Support\Facades\Route::bind('websiteSection', fn ($value) => \App\Models\WebsiteSection::findOrFail($value));
        \Illuminate\Support\Facades\Route::bind('item', fn ($value) => \App\Models\LibraryItem::findOrFail($value));
        \Illuminate\Support\Facades\Route::bind('libraryItem', fn ($value) => \App\Models\LibraryItem::findOrFail($value));
        \Illuminate\Support\Facades\Route::bind('job', fn ($value) => \App\Models\PlacementJob::findOrFail($value));
        \Illuminate\Support\Facades\Route::bind('application', fn ($value) => \App\Models\PlacementApplication::findOrFail($value));
        \Illuminate\Support\Facades\Route::bind('branch', fn ($value) => \App\Models\Branch::findOrFail($value));
        \Illuminate\Support\Facades\Route::bind('employee', fn ($value) => \App\Models\Employee::findOrFail($value));
        \Illuminate\Support\Facades\Route::bind('leave', fn ($value) => \App\Models\LeaveRequest::findOrFail($value));
        \Illuminate\Support\Facades\Route::bind('payroll', fn ($value) => \App\Models\PayrollRun::findOrFail($value));
        \Illuminate\Support\Facades\Route::bind('slip', fn ($value) => \App\Models\SalarySlip::findOrFail($value));
        \Illuminate\Support\Facades\Route::bind('entry', fn ($value) => \App\Models\FinanceLedgerEntry::findOrFail($value));
        \Illuminate\Support\Facades\Route::bind('receipt', fn ($value) => \App\Models\FinanceReceipt::findOrFail($value));

        \Illuminate\Support\Facades\View::composer('website.*', function () {
            try {
                \App\Services\WebsiteContentService::applyBrandToConfig();
            } catch (\Throwable $e) {
                // Table may not exist before migrate — keep config defaults.
            }
        });
    }
}
