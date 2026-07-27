<?php

namespace App\Console\Commands;

use App\Services\CertificateLifecycleService;
use Illuminate\Console\Command;

class UpdateCertificateStatuses extends Command
{
    protected $signature = 'certificates:update-statuses {--remind : Send expiry reminder notifications}';

    protected $description = 'Refresh certificate expiry statuses and optionally send renewal reminders';

    public function handle(CertificateLifecycleService $lifecycle): int
    {
        $updated = $lifecycle->refreshAllStatuses();
        $this->info("Updated {$updated} certificate status(es).");

        if ($this->option('remind')) {
            $sent = $lifecycle->sendExpiryReminders();
            $this->info("Sent {$sent} expiry reminder(s).");
        }

        return self::SUCCESS;
    }
}
