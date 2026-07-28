<?php

namespace App\Jobs;

use App\Models\Webinar;
use App\Models\WebinarRegistration;
use App\Services\AutomationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendWebinarRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(AutomationService $automations): void
    {
        $webinars = Webinar::query()
            ->where('registration_enabled', true)
            ->whereNotNull('starts_at')
            ->where('starts_at', '>', now())
            ->get();

        foreach ($webinars as $webinar) {
            $hours = max(1, (int) $webinar->reminder_hours_before);
            $windowStart = $webinar->starts_at->copy()->subHours($hours)->subMinutes(30);
            $windowEnd = $webinar->starts_at->copy()->subHours($hours)->addMinutes(30);

            if (! now()->between($windowStart, $windowEnd)) {
                continue;
            }

            $registrations = WebinarRegistration::where('webinar_id', $webinar->id)
                ->whereNull('reminder_sent_at')
                ->whereIn('status', ['registered', 'confirmed'])
                ->get();

            foreach ($registrations as $reg) {
                try {
                    Mail::raw(
                        "Reminder: {$webinar->title} starts at {$webinar->starts_at->format('M d, Y H:i')}. ".($webinar->meeting_url ?: ''),
                        function ($message) use ($reg, $webinar) {
                            $message->to($reg->email)->subject('Reminder: '.$webinar->title);
                        }
                    );
                    $reg->update(['reminder_sent_at' => now()]);
                } catch (\Throwable) {
                    // keep going
                }
            }
        }
    }
}
