<?php

namespace App\Notifications\Backup;

use NotificationChannels\Telegram\TelegramMessage;

trait HasTelegramNotification
{
    public function toTelegram($notifiable)
    {
        $message = TelegramMessage::create()
            ->to(env('TELEGRAM_CHAT_ID'));

        $className = class_basename($this);
        $isSuccess = str_contains($className, 'Successful') || str_contains($className, 'Healthy');
        
        $title = $isSuccess ? '✅ *Sukses!*' : '❌ *GAGAL!*';
        
        if (str_contains($className, 'Backup')) {
            $type = 'Proses Pencadangan (Backup)';
        } elseif (str_contains($className, 'Cleanup')) {
            $type = 'Pembersihan Cadangan Lama (Cleanup)';
        } else {
            $type = 'Pemeriksaan Kesehatan (Health Check)';
        }

        $content = "{$title}\n\n";
        $content .= "*Tugas:* {$type}\n";
        
        $properties = $this->backupDestinationProperties()->toArray();
        foreach ($properties as $key => $value) {
            $content .= "*{$key}:* {$value}\n";
        }
        
        if (!$isSuccess && isset($this->event->exception)) {
            $content .= "\n*Pesan Error:*\n`" . $this->event->exception->getMessage() . "`";
        }

        return $message->content($content);
    }
}
