<?php

namespace App\Notifications\Backup;

use App\Helpers\TelegramHelper;
use App\Services\Backup\CustomCleanupStrategy;
use NotificationChannels\Telegram\TelegramMessage;
use Spatie\Backup\BackupDestination\BackupDestination;
use Spatie\Backup\Helpers\Format;

trait HasTelegramNotification
{
    public function toTelegram($notifiable): TelegramMessage
    {
        $className = class_basename($this);
        $isSuccess = str_contains($className, 'Successful') || str_contains($className, 'Healthy');

        // Determine title & emoji based on notification class
        if (str_contains($className, 'Cleanup')) {
            $type = 'Cleanup Backup';
            $emoji = $isSuccess ? '🧹' : '❌';
            $title = $isSuccess ? 'CLEANUP SUKSES' : 'CLEANUP GAGAL';
        } elseif (str_contains($className, 'Healthy') || str_contains($className, 'Unhealthy')) {
            $type = 'Health Check';
            $emoji = $isSuccess ? '🩺' : '⚠️';
            $title = $isSuccess ? 'BACKUP SEHAT' : 'BACKUP KRITIS';
        } else {
            $type = 'Backup Data';
            $emoji = $isSuccess ? '✅' : '❌';
            $title = $isSuccess ? 'BACKUP SUKSES' : 'BACKUP GAGAL';
        }

        // ── Header ────────────────────────────────────────────
        $content = "{$emoji} <b>{$title}</b>\n\n";

        // ── Konten ────────────────────────────────────────────
        $content .= "📌 {$type}\n";

        $diskName = $this->event->diskName ?? null;
        $backupName = $this->event->backupName ?? null;

        if ($diskName) {
            $diskIcon = $diskName === 'google' ? '☁️' : '🗄';
            $diskLabel = $diskName === 'google' ? 'Google Drive' : 'Local';
            $content .= "{$diskIcon} <b>{$diskLabel}</b>\n";
        }

        if ($diskName && $backupName) {
            try {
                $dest = BackupDestination::create($diskName, $backupName);

                if (str_contains($className, 'Cleanup')) {
                    $freedBytes = CustomCleanupStrategy::$freedStorage[$diskName] ?? 0;
                    $freedCount = CustomCleanupStrategy::$deletedCounts[$diskName] ?? 0;
                    $freedFormatted = Format::humanReadableSize($freedBytes);
                    $usedStorage = Format::humanReadableSize($dest->fresh()->usedStorage());

                    if ($freedCount > 0) {
                        $content .= "🗑 <b>Ruang Dihapus:</b> {$freedFormatted} ({$freedCount} file backup)\n";
                    } else {
                        $content .= "🗑 <b>Ruang Dihapus:</b> 0 B (Tidak ada file kedaluwarsa)\n";
                    }
                    $content .= "💾 <b>Total Backup Tersimpan:</b> {$usedStorage}\n";
                }

                $newest = $dest->newestBackup();
                if ($newest && ! str_contains($className, 'Cleanup')) {
                    $size = Format::humanReadableSize($newest->sizeInBytes());
                    $content .= '📦 <code>'.basename($newest->path())."</code>\n";
                    $content .= "💾 {$size}\n";
                }
            } catch (\Exception $e) {
                // ignore if backup info unavailable
            }
        }

        if (! $isSuccess && isset($this->event->exception)) {
            $content .= "\n⚠️ <code>".substr($this->event->exception->getMessage(), 0, 200).'</code>';
        }

        // ── Footer ────────────────────────────────────────────
        $content = rtrim($content).TelegramHelper::footer();

        return TelegramMessage::create()
            ->to(config('services.telegram-bot-api.chat_id'))
            ->options(['parse_mode' => 'HTML'])
            ->content($content);
    }
}
