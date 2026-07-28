<?php

namespace App\Services;

use App\Models\Community;
use App\Models\CommunityAnnouncement;
use Illuminate\Support\Facades\Http;

class TelegramService
{
    public function __construct(protected IntegrationService $integrations) {}

    public function config(?int $userId = null): array
    {
        return $this->integrations->get('telegram', $userId);
    }

    public function sendMessage(?int $userId, string $chatId, string $text): array
    {
        $cfg = $this->config($userId);
        $token = $cfg['bot_token'] ?? null;
        if (! $token) {
            return ['ok' => false, 'message' => 'Telegram bot token not configured.'];
        }

        try {
            $res = Http::timeout(15)->asForm()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ]);

            if ($res->successful() && $res->json('ok')) {
                return ['ok' => true, 'message' => 'Message sent.'];
            }

            return ['ok' => false, 'message' => $res->json('description') ?? 'Send failed'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }

    public function pushAnnouncement(CommunityAnnouncement $announcement, ?int $userId = null): array
    {
        $community = $announcement->community;
        if (! $community?->telegram_push_enabled) {
            return ['ok' => false, 'message' => 'Telegram push disabled for this community.'];
        }

        $chatId = $community->telegram_chat_id ?: ($this->config($userId)['default_chat_id'] ?? null);
        if (! $chatId) {
            return ['ok' => false, 'message' => 'No Telegram chat id configured.'];
        }

        $text = '<b>'.e($announcement->title)."</b>\n\n".e($announcement->body);
        $result = $this->sendMessage($userId, $chatId, $text);

        if ($result['ok']) {
            $announcement->update([
                'pushed_to_telegram' => true,
                'telegram_pushed_at' => now(),
            ]);
        }

        return $result;
    }

    public function sendTest(?int $userId, ?string $chatId = null): array
    {
        $cfg = $this->config($userId);
        $chatId = $chatId ?: ($cfg['default_chat_id'] ?? null);
        if (! $chatId) {
            return ['ok' => false, 'message' => 'Provide a chat id or set default_chat_id.'];
        }

        return $this->sendMessage($userId, $chatId, 'StudyNest test announcement — Telegram connected.');
    }
}
