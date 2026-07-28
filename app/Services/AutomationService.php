<?php

namespace App\Services;

use App\Contracts\SmsProviderInterface;
use App\Contracts\WhatsAppProviderInterface;
use App\Models\AutomationRun;
use App\Models\AutomationWorkflow;
use App\Models\Coupon;
use App\Models\Lead;
use App\Models\LeadFollowUp;
use App\Models\Segment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class AutomationService
{
    public function __construct(
        protected SmsProviderInterface $sms,
        protected WhatsAppProviderInterface $whatsapp
    ) {}

    public function dispatch(string $triggerKey, ?int $instituteUserId, ?Model $subject = null, array $context = []): int
    {
        $query = AutomationWorkflow::query()
            ->where('trigger_key', $triggerKey)
            ->where('is_active', true);

        if ($instituteUserId) {
            $query->where('created_by', $instituteUserId);
        }

        $ran = 0;
        foreach ($query->get() as $workflow) {
            $this->run($workflow, $subject, $context);
            $ran++;
        }

        return $ran;
    }

    public function run(AutomationWorkflow $workflow, ?Model $subject = null, array $context = []): AutomationRun
    {
        $run = AutomationRun::create([
            'automation_workflow_id' => $workflow->id,
            'trigger_key' => $workflow->trigger_key,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'status' => 'running',
            'context' => $context,
        ]);

        $results = [];

        try {
            foreach ($workflow->actions ?? [] as $action) {
                $results[] = $this->executeAction($workflow, $action, $subject, $context);
            }

            $run->update(['status' => 'completed', 'result' => $results]);
            $workflow->update([
                'run_count' => $workflow->run_count + 1,
                'last_run_at' => now(),
            ]);
        } catch (\Throwable $e) {
            $run->update([
                'status' => 'failed',
                'error' => $e->getMessage(),
                'result' => $results,
            ]);
        }

        return $run->fresh();
    }

    protected function executeAction(AutomationWorkflow $workflow, array $action, ?Model $subject, array $context): array
    {
        $type = $action['type'] ?? '';
        $email = $context['email'] ?? ($subject->email ?? null);
        $phone = $context['phone'] ?? ($subject->phone ?? null);
        $name = $context['name'] ?? ($subject->name ?? 'there');

        return match ($type) {
            'send_email' => $this->sendEmail($email, $action, $name),
            'send_sms' => ['type' => 'send_sms', 'result' => $this->sms->send((string) $phone, $this->parse($action['message'] ?? '', $name))],
            'send_whatsapp' => ['type' => 'send_whatsapp', 'result' => $this->whatsapp->send((string) $phone, $this->parse($action['message'] ?? '', $name))],
            'create_follow_up' => $this->createFollowUp($workflow, $subject, $action, $context),
            'add_segment' => $this->addSegment($subject, $action, $context),
            'award_coupon' => $this->noteCoupon($action, $context),
            default => ['type' => $type, 'skipped' => true],
        };
    }

    protected function sendEmail(?string $email, array $action, string $name): array
    {
        if (! $email) {
            return ['type' => 'send_email', 'skipped' => true, 'reason' => 'no email'];
        }

        $subject = $this->parse($action['subject'] ?? 'Hello', $name);
        $body = $this->parse($action['message'] ?? '', $name);

        Mail::raw($body, function ($message) use ($email, $subject) {
            $message->to($email)->subject($subject);
        });

        return ['type' => 'send_email', 'to' => $email, 'success' => true];
    }

    protected function createFollowUp(AutomationWorkflow $workflow, ?Model $subject, array $action, array $context): array
    {
        $lead = $subject instanceof Lead
            ? $subject
            : Lead::where('email', $context['email'] ?? '')->where('created_by', $workflow->created_by)->latest()->first();

        if (! $lead) {
            return ['type' => 'create_follow_up', 'skipped' => true];
        }

        $fu = LeadFollowUp::create([
            'lead_id' => $lead->id,
            'assigned_to' => $lead->assigned_to ?: $workflow->created_by,
            'created_by' => $workflow->created_by,
            'title' => $action['title'] ?? 'Automation follow-up',
            'notes' => $action['notes'] ?? null,
            'due_at' => now()->addHours((int) ($action['due_hours'] ?? 24)),
            'status' => 'pending',
        ]);

        return ['type' => 'create_follow_up', 'follow_up_id' => $fu->id];
    }

    protected function addSegment(?Model $subject, array $action, array $context): array
    {
        $segmentId = $action['segment_id'] ?? null;
        $segment = $segmentId ? Segment::find($segmentId) : null;
        $user = $subject instanceof User
            ? $subject
            : User::where('email', $context['email'] ?? '')->first();

        if (! $segment || ! $user) {
            return ['type' => 'add_segment', 'skipped' => true];
        }

        $segment->users()->syncWithoutDetaching([$user->id]);

        return ['type' => 'add_segment', 'segment_id' => $segment->id, 'user_id' => $user->id];
    }

    protected function noteCoupon(array $action, array $context): array
    {
        $code = $action['coupon_code'] ?? null;
        $coupon = $code ? Coupon::where('code', strtoupper($code))->first() : null;

        return [
            'type' => 'award_coupon',
            'coupon' => $coupon?->code,
            'note' => $coupon
                ? "Coupon {$coupon->code} reserved for {$context['email'] ?? 'recipient'} (manual redeem)."
                : 'Coupon not found',
        ];
    }

    protected function parse(string $text, string $name): string
    {
        return str_replace(['{{name}}', '{{Name}}'], $name, $text);
    }
}
