<?php

namespace App\Notifications\Backup;

use App\Helpers\TelegramHelper;
use NotificationChannels\Telegram\TelegramMessage;

trait HasTelegramNotification
{
    public function toTelegram($notifiable): TelegramMessage
    {
        $className = class_basename($this);
        $isSuccess = str_contains($className, 'Successful') || str_contains($className, 'Healthy');

        // Determine title & emoji based on notification class
        if (str_contains($className, 'Cleanup')) {
            $type  = 'Cleanup Arsip';
            $emoji = $isSuccess ? '🧹' : '❌';
            $title = $isSuccess ? 'CLEANUP SUKSES' : 'CLEANUP GAGAL';
        } elseif (str_contains($className, 'Healthy') || str_contains($className, 'Unhealthy')) {
            $type  = 'Health Check';
            $emoji = $isSuccess ? '🩺' : '⚠️';
            $title = $isSuccess ? 'BACKUP SEHAT' : 'BACKUP KRITIS';
        } else {
            $type  = 'Backup Data';
            $emoji = $isSuccess ? '✅' : '❌';
            $title = $isSuccess ? 'BACKUP SUKSES' : 'BACKUP GAGAL';
        }

        // ── Header ────────────────────────────────────────────
        $content = "{$emoji} <b>{$title}</b>\n\n";

        // ── Konten ────────────────────────────────────────────
        $content .= "📌 {$type}\n";

        $diskName   = $this->event->diskName   ?? null;
        $backupName = $this->event->backupName ?? null;

        if ($diskName) {
            $diskIcon = $diskName === 'google' ? '☁️' : '🗄';
            $diskLabel = $diskName === 'google' ? 'Google Drive' : 'Local';
            $content .= "{$diskIcon} <b>{$diskLabel}</b>\n";
        }

        if ($diskName && $backupName) {
            try {
                $dest   = \Spatie\Backup\BackupDestination\BackupDestination::create($diskName, $backupName);
                $newest = $dest->newestBackup();
                if ($newest) {
                    $size     = \Spatie\Backup\Helpers\Format::humanReadableSize($newest->sizeInBytes());
                    if (str_contains($className, 'Cleanup')) {
                        $content .= "📂 Backup Terbaru:\n";
                        $content .= "   └ 📦 <code>" . basename($newest->path()) . "</code>\n";
                        $content .= "   └ 💾 Ukuran: {$size}\n";
                    } else {
                        $content .= "📦 <code>" . basename($newest->path()) . "</code>\n";
                        $content .= "💾 {$size}\n";
                    }
                }
            } catch (\Exception $e) {
                // ignore if backup info unavailable
            }
        }

        if (! $isSuccess && isset($this->event->exception)) {
            $content .= "\n⚠️ <code>" . substr($this->event->exception->getMessage(), 0, 200) . "</code>";
        }

        // ── Footer ────────────────────────────────────────────
        $content = rtrim($content) . TelegramHelper::footer();

        return TelegramMessage::create()
            ->to(config('services.telegram-bot-api.chat_id'))
            ->options(['parse_mode' => 'HTML'])
            ->content($content);
    }
}
