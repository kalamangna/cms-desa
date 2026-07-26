<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class SystemMonitorNotification extends Notification
{
    public function __construct(
        public string $title,
        public string $message,
        public string $type = 'info' // info, warning, danger, success
    ) {}

    public function via($notifiable)
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable)
    {
        $emoji = match($this->type) {
            'danger' => '🚨',
            'warning' => '⚠️',
            'success' => '✅',
            default => 'ℹ️',
        };

        $appName = env('APP_NAME', 'Website Desa');
        try {
            $settings = \Illuminate\Support\Facades\DB::table('settings')->pluck('value', 'key')->toArray();
            if (!empty($settings['village_name'])) {
                $appName = "Desa " . $settings['village_name'];
            }
        } catch (\Exception $e) {
            // Abaikan jika database belum siap
        }

        $date = now()->setTimezone('Asia/Makassar')->format('d M Y, H:i');

        $content = "{$emoji} <b>{$this->title}</b>\n\n";
        $content .= "🏢 <b>Website:</b> {$appName}\n";
        $content .= "🕒 <b>Waktu:</b> {$date}\n\n";
        $content .= $this->message;

        return TelegramMessage::create()
            ->to(config('services.telegram-bot-api.chat_id'))
            ->options(['parse_mode' => 'HTML'])
            ->content($content);
    }
}
