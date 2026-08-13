<?php

namespace App\Notifications;

use App\Helpers\TelegramHelper;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class SystemMonitorNotification extends Notification
{
    public function __construct(
        public string $title,
        public string $message,
        public string $type = 'info' // info, warning, danger, success
    ) {}

    public function via($notifiable): array
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable): TelegramMessage
    {
        $emoji = match ($this->type) {
            'danger' => '🚨',
            'warning' => '⚠️',
            'success' => '✅',
            default => 'ℹ️',
        };

        // ── Header ────────────────────────────────────────────
        $content = "{$emoji} <b>{$this->title}</b>\n\n";

        // ── Konten ────────────────────────────────────────────
        $content .= $this->message;

        // ── Footer ────────────────────────────────────────────
        $content = rtrim($content).TelegramHelper::footer();

        return TelegramMessage::create()
            ->to(config('services.telegram-bot-api.chat_id'))
            ->options(['parse_mode' => 'HTML'])
            ->content($content);
    }
}
