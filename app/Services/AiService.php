<?php

namespace App\Services;

use App\Models\AiGeneration;
use App\Models\Setting;
use App\Models\SubscriptionPackage;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class AiService
{
    public function isEnabledFor(?int $instituteUserId = null): bool
    {
        $flag = Setting::get('ai_enabled', '1', 'ai');
        if ($flag === '0' || $flag === false) {
            return false;
        }

        // Premium gate: if packages mention AI feature, require at least one active package with ai in features (soft check)
        $packages = SubscriptionPackage::active()->get();
        if ($packages->isEmpty()) {
            return true;
        }

        $aiPackages = $packages->filter(function (SubscriptionPackage $p) {
            $features = strtolower(implode(' ', $p->featureList()));

            return str_contains($features, 'ai') || ($p->meta['ai_enabled'] ?? false);
        });

        // If no package advertises AI, allow; if some do, allow institutes by default (billing not enforced here)
        return true;
    }

    public function getConfig(?int $instituteUserId = null): array
    {
        $group = $instituteUserId ? 'ai_'.$instituteUserId : 'ai';
        $keyEncrypted = Setting::get('api_key', null, $group) ?: Setting::get('api_key', null, 'ai');
        $apiKey = null;
        if ($keyEncrypted) {
            try {
                $apiKey = Crypt::decryptString($keyEncrypted);
            } catch (\Throwable) {
                $apiKey = $keyEncrypted;
            }
        }

        return [
            'base_url' => rtrim((string) (Setting::get('base_url', 'https://api.openai.com/v1', $group) ?: Setting::get('base_url', 'https://api.openai.com/v1', 'ai')), '/'),
            'api_key' => $apiKey,
            'model' => Setting::get('model', 'gpt-4o-mini', $group) ?: Setting::get('model', 'gpt-4o-mini', 'ai'),
            'enabled' => $this->isEnabledFor($instituteUserId),
        ];
    }

    public function saveConfig(array $data, ?int $instituteUserId = null): void
    {
        $group = $instituteUserId ? 'ai_'.$instituteUserId : 'ai';

        if (! empty($data['base_url'])) {
            Setting::set('base_url', rtrim($data['base_url'], '/'), $group);
        }
        if (! empty($data['model'])) {
            Setting::set('model', $data['model'], $group);
        }
        if (array_key_exists('enabled', $data)) {
            Setting::set('ai_enabled', $data['enabled'] ? '1' : '0', $group);
        }
        if (! empty($data['api_key'])) {
            Setting::set('api_key', Crypt::encryptString($data['api_key']), $group, 'encrypted');
        }
    }

    public function generate(User $user, string $feature, string $prompt, ?int $instituteUserId = null, ?int $courseId = null, ?string $title = null): AiGeneration
    {
        $instituteUserId = $instituteUserId ?: $user->created_by ?: $user->id;
        $config = $this->getConfig($instituteUserId);

        if (! $config['enabled']) {
            throw ValidationException::withMessages(['ai' => 'AI Center is disabled for this institute.']);
        }

        $system = $this->systemPrompt($feature);
        $output = $this->chat($config, $system, $prompt);

        return AiGeneration::create([
            'created_by' => $instituteUserId,
            'user_id' => $user->id,
            'feature' => $feature,
            'title' => $title ?: ucfirst(str_replace('_', ' ', $feature)),
            'prompt' => $prompt,
            'output' => $output,
            'status' => 'draft',
            'course_id' => $courseId,
            'meta' => ['model' => $config['model']],
        ]);
    }

    public function chat(array $config, string $system, string $userMessage, array $history = []): string
    {
        if (empty($config['api_key'])) {
            return $this->offlineFallback($system, $userMessage);
        }

        $messages = array_merge(
            [['role' => 'system', 'content' => $system]],
            $history,
            [['role' => 'user', 'content' => $userMessage]]
        );

        try {
            $response = Http::withToken($config['api_key'])
                ->timeout(60)
                ->post($config['base_url'].'/chat/completions', [
                    'model' => $config['model'],
                    'messages' => $messages,
                    'temperature' => 0.7,
                ]);

            if (! $response->successful()) {
                return $this->offlineFallback($system, $userMessage, 'API error: '.$response->status());
            }

            return (string) data_get($response->json(), 'choices.0.message.content', '');
        } catch (\Throwable $e) {
            return $this->offlineFallback($system, $userMessage, $e->getMessage());
        }
    }

    protected function systemPrompt(string $feature): string
    {
        return match ($feature) {
            'course_outline' => 'You are an expert instructional designer. Produce a clear course outline with modules and lessons.',
            'quiz' => 'You are an exam setter. Generate multiple-choice questions with options and correct answers marked.',
            'notes' => 'You are a tutor. Produce concise study notes with headings and bullet points.',
            'assignment' => 'You are an educator. Create a practical assignment with objectives, tasks, and rubric.',
            'doubt_chat' => 'You are a helpful teaching assistant. Answer the learner doubt clearly and briefly.',
            'study_planner' => 'You are a study coach. Build a realistic day-by-day study plan.',
            'performance' => 'You are a learning analyst. Suggest improvements based on the learner context provided.',
            default => 'You are a helpful education assistant for an LMS.',
        };
    }

    protected function offlineFallback(string $system, string $prompt, ?string $reason = null): string
    {
        $note = $reason ? "\n\n(Provider note: {$reason})" : "\n\n(Offline draft — configure AI API key in AI Center settings.)";

        return "## Draft output\n\nBased on your request:\n\n{$prompt}\n\nSuggested structure:\n1. Introduction\n2. Core concepts\n3. Practice tasks\n4. Summary\n{$note}";
    }
}
