<?php

namespace App\Notifications\Backup;

use NotificationChannels\Telegram\TelegramMessage;

trait HasTelegramNotification
{
    public function toTelegram($notifiable)
    {
        $message = TelegramMessage::create()
            ->to(config('services.telegram-bot-api.chat_id'))
            ->options(['parse_mode' => 'HTML']);

        $className = class_basename($this);
        $isSuccess = str_contains($className, 'Successful') || str_contains($className, 'Healthy');
        
        $title = $isSuccess ? '✅ <b>BACKUP SUKSES</b>' : '❌ <b>BACKUP GAGAL</b>';
        
        if (str_contains($className, 'Backup')) {
            $type = 'Backup Data';
        } elseif (str_contains($className, 'Cleanup')) {
            $type = 'Pembersihan Arsip (Cleanup)';
            $title = $isSuccess ? '🧹 <b>PEMBERSIHAN SUKSES</b>' : '❌ <b>PEMBERSIHAN GAGAL</b>';
        } else {
            $type = 'Pemeriksaan Kesehatan (Health Check)';
            $title = $isSuccess ? '🩺 <b>KONDISI SEHAT</b>' : '⚠️ <b>KONDISI KRITIS</b>';
        }

        $villageName = 'Tidak Diketahui';
        try {
            $settings = \Illuminate\Support\Facades\DB::table('settings')->pluck('value', 'key')->toArray();
            if (!empty($settings['village_name'])) {
                $villageName = $settings['village_name'];
            }
        } catch (\Exception $e) {
            // Abaikan jika database belum siap
        }
        
        $appName = "Desa {$villageName}";
        $date = now()->setTimezone('Asia/Makassar')->format('d M Y, H:i');

        $content = "{$title}\n\n";
        $content .= "🏢 <b>Website:</b> {$appName}\n";
        $content .= "📌 <b>Tugas:</b> {$type}\n";
        $content .= "🕒 <b>Waktu:</b> {$date}\n";
        
        $diskName = $this->event->diskName ?? null;
        $backupName = $this->event->backupName ?? null;
        
        if ($diskName && $backupName) {
            $dest = \Spatie\Backup\BackupDestination\BackupDestination::create($diskName, $backupName);
            $newest = $dest->newestBackup();
            if ($newest) {
                $content .= "📦 <b>File:</b> <code>" . basename($newest->path()) . "</code>\n";
                $content .= "💾 <b>Ukuran:</b> " . \Spatie\Backup\Helpers\Format::humanReadableSize($newest->sizeInBytes()) . "\n";
            }
        }
        
        if (!$isSuccess && isset($this->event->exception)) {
            $content .= "\n⚠️ <b>Pesan Error:</b>\n<pre>" . $this->event->exception->getMessage() . "</pre>";
        }

        return $message->content($content);
    }
}
