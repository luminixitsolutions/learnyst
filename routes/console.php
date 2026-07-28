<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('certificates:update-statuses --remind')->dailyAt('06:00');
Schedule::job(new \App\Jobs\SendWebinarRemindersJob)->hourly();
