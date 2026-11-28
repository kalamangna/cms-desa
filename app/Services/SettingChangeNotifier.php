<?php

namespace App\Services;

use App\Models\Setting;
use App\Notifications\SystemMonitorNotification;
use Illuminate\Support\Facades\Notification;

class SettingChangeNotifier
{
    private const IGNORED_KEYS = [
        'sejarah_desa',
        'visi_misi',
        'peta_desa',
    ];

    private const MAX_ITEMS_IN_MESSAGE = 10;

    /**
     * @var array<string, array{old: string, new: string}>
     */
    private array $changes = [];

    /**
     * Mencatat perubahan pengaturan ke buffer per request agar notifikasi Telegram dikirim ringkas sekaligus.
     */
    public function record(Setting $setting): void
    {
        if (in_array($setting->key, self::IGNORED_KEYS, true)) {
            return;
        }

        $oldRaw = (string) ($setting->getOriginal('value') ?? '');
        $newRaw = (string) ($setting->value ?? '');

        if ($oldRaw === $newRaw) {
            return;
        }

        $isSensitive = (bool) preg_match('/(?:nik|password|secret|token|passphrase|key)$/i', $setting->key);

        $formattedOld = $isSensitive ? '[TERSEMBUNYI]' : $this->formatValue($oldRaw);
        $formattedNew = $isSensitive ? '[TERSEMBUNYI]' : $this->formatValue($newRaw);

        $this->changes[$setting->key] = [
            'old' => $this->changes[$setting->key]['old'] ?? $formattedOld,
            'new' => $formattedNew,
        ];
    }

    /**
     * Menyusun teks ringkasan perubahan pengaturan dalam format HTML Telegram.
     */
    public function buildSummaryMessage(?string $actorName = null): ?string
    {
        if ($this->changes === []) {
            return null;
        }

        $lines = [];
        if (filled($actorName)) {
            $lines[] = '👤 Oleh: <b>'.e($actorName).'</b>';
        }
        $lines[] = '📊 Jumlah diubah: <b>'.count($this->changes).'</b>';
        $lines[] = '';

        $visibleChanges = array_slice($this->changes, 0, self::MAX_ITEMS_IN_MESSAGE, true);
        foreach ($visibleChanges as $key => $item) {
            $lines[] = '• <code>'.e($key).'</code>: <code>'.$item['old'].'</code> → <code>'.$item['new'].'</code>';
        }

        $remaining = count($this->changes) - count($visibleChanges);
        if ($remaining > 0) {
            $lines[] = '… dan '.$remaining.' pengaturan lainnya.';
        }

        return implode("\n", $lines);
    }

    /**
     * Mengirim satu ringkasan notifikasi Telegram untuk seluruh perubahan pengaturan pada request aktif.
     */
    public function flush(?string $actorName = null): bool
    {
        $message = $this->buildSummaryMessage($actorName);
        if ($message === null) {
            return false;
        }

        Notification::route('telegram', 'system')->notify(
            new SystemMonitorNotification('PENGATURAN DIUBAH', $message, 'info')
        );

        $this->reset();

        return true;
    }

    public function reset(): void
    {
        $this->changes = [];
    }

    private function formatValue(string $value): string
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return '-';
        }

        $shortened = mb_strlen($trimmed) > 60
            ? mb_substr($trimmed, 0, 60).'…'
            : $trimmed;

        return e($shortened);
    }
}
