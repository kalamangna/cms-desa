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
        
        $title = $isSuccess ? '✅ <b>PENCADANGAN SUKSES</b>' : '❌ <b>PENCADANGAN GAGAL</b>';
        
        if (str_contains($className, 'Backup')) {
            $type = 'Proses Pencadangan (Backup)';
        } elseif (str_contains($className, 'Cleanup')) {
            $type = 'Pembersihan Arsip (Cleanup)';
            $title = $isSuccess ? '🧹 <b>PEMBERSIHAN SUKSES</b>' : '❌ <b>PEMBERSIHAN GAGAL</b>';
        } else {
            $type = 'Pemeriksaan Kesehatan (Health Check)';
            $title = $isSuccess ? '🩺 <b>KONDISI SEHAT</b>' : '⚠️ <b>KONDISI KRITIS</b>';
        }

        $appName = env('APP_NAME', 'Website Desa');
        $date = now()->setTimezone('Asia/Makassar')->format('d M Y, H:i');

        $content = "{$title}\n\n";
        $content .= "🏢 <b>Website:</b> {$appName}\n";
        $content .= "📌 <b>Tugas:</b> {$type}\n";
        $content .= "🕒 <b>Waktu:</b> {$date}\n";
        
        $properties = collect($this->backupDestinationProperties()->toArray());
        
        if ($properties->has('Nama cadangan') && $properties->get('Nama cadangan')) {
            $content .= "📦 <b>File:</b> <code>" . $properties->get('Nama cadangan') . "</code>\n";
        }
        
        if ($properties->has('Ukuran cadangan terbaru')) {
            $size = $properties->get('Ukuran cadangan terbaru');
            if (!str_contains(strtolower($size), 'belum ada')) {
                $content .= "💾 <b>Ukuran:</b> {$size}\n";
            }
        }
        
        if (!$isSuccess && isset($this->event->exception)) {
            $content .= "\n⚠️ <b>Pesan Error:</b>\n<pre>" . $this->event->exception->getMessage() . "</pre>";
        }

        return $message->content($content);
    }
}
