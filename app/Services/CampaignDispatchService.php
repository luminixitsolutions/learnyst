<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\CampaignSend;
use App\Models\Lead;
use App\Models\Segment;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class CampaignDispatchService
{
    public function dispatch(Campaign $campaign): Campaign
    {
        if (in_array($campaign->status, ['sent', 'sending'], true)) {
            throw ValidationException::withMessages(['campaign' => 'Campaign already sent or in progress.']);
        }

        $recipients = $this->resolveRecipients($campaign);
        $campaign->update([
            'status' => 'sending',
            'audience_count' => $recipients->count(),
        ]);

        $sent = 0;
        $failed = 0;
        $channels = $this->channelsFor($campaign->channel);

        foreach ($recipients as $recipient) {
            foreach ($channels as $channel) {
                $result = $this->sendToRecipient($campaign, $recipient, $channel);
                if ($result === 'sent') {
                    $sent++;
                } elseif ($result === 'failed') {
                    $failed++;
                }
            }
        }

        $campaign->update([
            'status' => $failed && ! $sent ? 'failed' : 'sent',
            'sent_count' => $sent,
            'failed_count' => $failed,
            'sent_at' => now(),
        ]);

        ActivityLogger::log('campaign_sent', "Campaign {$campaign->title} dispatched", $campaign, [
            'sent' => $sent,
            'failed' => $failed,
            'audience' => $recipients->count(),
        ]);

        return $campaign->fresh();
    }

    protected function resolveRecipients(Campaign $campaign): Collection
    {
        if ($campaign->segment_id) {
            $segment = Segment::with('users')->find($campaign->segment_id);
            if ($segment) {
                return $segment->users
                    ->filter(fn (User $u) => filled($u->email) || filled($u->phone))
                    ->map(fn (User $u) => [
                        'type' => 'user',
                        'user_id' => $u->id,
                        'lead_id' => null,
                        'email' => $u->email,
                        'phone' => $u->phone,
                        'name' => $u->name,
                    ])
                    ->values();
            }
        }

        $leadQuery = Lead::query()
            ->whereNotIn('status', ['converted', 'lost'])
            ->when($campaign->created_by, fn ($q) => $q->where(function ($inner) use ($campaign) {
                $inner->where('created_by', $campaign->created_by)
                    ->orWhereNull('created_by');
            }));

        return $leadQuery->get()->map(fn (Lead $lead) => [
            'type' => 'lead',
            'user_id' => null,
            'lead_id' => $lead->id,
            'email' => $lead->email,
            'phone' => $lead->phone,
            'name' => $lead->name,
        ]);
    }

    protected function channelsFor(string $channel): array
    {
        return match ($channel) {
            'email' => ['email'],
            'sms' => ['sms'],
            'whatsapp' => ['whatsapp'],
            'both' => ['email', 'whatsapp'],
            'email_sms' => ['email', 'sms'],
            'all' => ['email', 'sms', 'whatsapp'],
            default => ['email'],
        };
    }

    protected function sendToRecipient(Campaign $campaign, array $recipient, string $channel): string
    {
        $address = $channel === 'email' ? ($recipient['email'] ?? null) : ($recipient['phone'] ?? null);

        if (! $address) {
            CampaignSend::create([
                'campaign_id' => $campaign->id,
                'user_id' => $recipient['user_id'],
                'lead_id' => $recipient['lead_id'],
                'channel' => $channel,
                'recipient' => null,
                'status' => 'skipped',
                'error' => 'Missing recipient for channel '.$channel,
            ]);

            return 'skipped';
        }

        try {
            if ($channel === 'email') {
                $subject = $campaign->subject ?: $campaign->title;
                $body = $campaign->content ?: $campaign->title;
                Mail::raw($body, function ($message) use ($address, $subject) {
                    $message->to($address)->subject($subject);
                });
            }
            // SMS / WhatsApp: log as sent (provider wiring in Phase D / A4)

            CampaignSend::create([
                'campaign_id' => $campaign->id,
                'user_id' => $recipient['user_id'],
                'lead_id' => $recipient['lead_id'],
                'channel' => $channel,
                'recipient' => $address,
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return 'sent';
        } catch (\Throwable $e) {
            CampaignSend::create([
                'campaign_id' => $campaign->id,
                'user_id' => $recipient['user_id'],
                'lead_id' => $recipient['lead_id'],
                'channel' => $channel,
                'recipient' => $address,
                'status' => 'failed',
                'error' => $e->getMessage(),
            ]);

            return 'failed';
        }
    }
}
