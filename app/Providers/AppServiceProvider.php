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
        //
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

        \Illuminate\Support\Facades\View::composer('website.*', function () {
            try {
                \App\Services\WebsiteContentService::applyBrandToConfig();
            } catch (\Throwable $e) {
                // Table may not exist before migrate — keep config defaults.
            }
        });
    }
}
